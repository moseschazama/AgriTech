<?php
// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "order_number",
        "buyer_id",
        "seller_id",
        "status",
        "subtotal",
        "delivery_fee",
        "total",
        "currency",
        "payment_method",
        "payment_status",
        "payment_reference",
        "paid_at",
        "delivery_district",
        "delivery_address",
        "delivery_lat",
        "delivery_lng",
        "confirmed_at",
        "dispatched_at",
        "delivered_at",
        "cancelled_at",
        "cancellation_reason",
        "buyer_notes",
    ];

    protected $casts = [
        "subtotal" => "decimal:2",
        "delivery_fee" => "decimal:2",
        "total" => "decimal:2",
        "paid_at" => "datetime",
        "confirmed_at" => "datetime",
        "dispatched_at" => "datetime",
        "delivered_at" => "datetime",
        "cancelled_at" => "datetime",
    ];

    /** Valid forward-only status transitions (state machine). */
    public const STATUS_FLOW = [
        "pending" => ["confirmed", "cancelled"],
        "confirmed" => ["processing", "cancelled"],
        "processing" => ["dispatched", "cancelled"],
        "dispatched" => ["in_transit"],
        "in_transit" => ["delivered"],
        "delivered" => ["refunded"],
        "cancelled" => [],
        "refunded" => [],
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Order $order) {
            $order->order_number =
                $order->order_number ?:
                "ORD-" .
                    now()->format("y") .
                    "-" .
                    str_pad((static::max("id") ?? 0) + 1, 5, "0", STR_PAD_LEFT);
        });
    }

    // ── Relationships ─────────────────────────────────────────────────

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, "buyer_id");
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, "seller_id");
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class, "related_id")->where(
            "related_type",
            self::class,
        );
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopeForBuyer($query, int $userId)
    {
        return $query->where("buyer_id", $userId);
    }

    public function scopeForSeller($query, int $userId)
    {
        return $query->where("seller_id", $userId);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn("status", [
            "delivered",
            "cancelled",
            "refunded",
        ]);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where("status", $status);
    }

    // ── State Machine ──────────────────────────────────────────────────

    /**
     * Check whether transitioning to $newStatus is allowed from current status.
     */
    public function canTransitionTo(string $newStatus): bool
    {
        return in_array(
            $newStatus,
            self::STATUS_FLOW[$this->status] ?? [],
            true,
        );
    }

    /**
     * Transition the order to a new status — validates the state machine,
     * stamps the relevant timestamp, fires the SMS notification and
     * applies side-effects (stock restore on cancel, etc).
     *
     * @throws \Exception if transition is invalid
     */
    public function transitionTo(
        string $newStatus,
        ?string $reason = null,
    ): void {
        if (!$this->canTransitionTo($newStatus)) {
            throw new \Exception(
                "Cannot transition order from '{$this->status}' to '{$newStatus}'.",
            );
        }

        $timestampField = match ($newStatus) {
            "confirmed" => "confirmed_at",
            "dispatched" => "dispatched_at",
            "delivered" => "delivered_at",
            "cancelled" => "cancelled_at",
            default => null,
        };

        $updates = ["status" => $newStatus];
        if ($timestampField) {
            $updates[$timestampField] = now();
        }
        if ($newStatus === "cancelled") {
            $updates["cancellation_reason"] = $reason;
        }

        $this->update($updates);

        // Side-effects per transition
        match ($newStatus) {
            "cancelled" => $this->restoreStockForItems(),
            "delivered" => $this->onDelivered(),
            default => null,
        };

        // Dispatch SMS notification via the SmsService
        app(\App\Services\SmsService::class)->sendOrderStatusUpdate($this);
    }

    /** Restore product stock for every item when an order is cancelled. */
    protected function restoreStockForItems(): void
    {
        foreach ($this->items as $item) {
            $item->product?->restoreStock($item->quantity);
        }
    }

    /** Side effects when an order reaches "delivered". */
    protected function onDelivered(): void
    {
        $this->seller->increment("total_orders");

        Notification::create([
            "user_id" => $this->buyer_id,
            "title" => "Order Delivered! 📦",
            "message" => "Your order #{$this->order_number} has been delivered. Enjoy!",
            "type" => "order",
            "icon" => "fas fa-check-circle",
            "icon_color" => "var(--success)",
        ]);
    }

    // ── Business Logic ────────────────────────────────────────────────

    /**
     * Build an order from a cart array: [['product_id'=>1,'quantity'=>2], ...]
     * Validates stock, computes totals, creates order + items atomically,
     * and reduces stock for each product.
     *
     * @throws \Exception on validation failure
     */
    public static function createFromCart(
        User $buyer,
        array $cartItems,
        array $deliveryAddress,
        ?string $paymentMethod = null,
    ): self {
        return \DB::transaction(function () use (
            $buyer,
            $cartItems,
            $deliveryAddress,
            $paymentMethod,
        ) {
            if (empty($cartItems)) {
                throw new \Exception("Cart is empty.");
            }

            // Load and validate all products first (fail fast before writing anything)
            $products = Product::whereIn(
                "id",
                array_column($cartItems, "product_id"),
            )
                ->lockForUpdate()
                ->get()
                ->keyBy("id");

            $subtotal = 0;
            foreach ($cartItems as $item) {
                $product =
                    $products->get($item["product_id"]) ??
                    throw new \Exception(
                        "Product #{$item["product_id"]} not found.",
                    );

                if (!$product->hasStock($item["quantity"])) {
                    throw new \Exception(
                        "Insufficient stock for \"{$product->name}\".",
                    );
                }
                $subtotal += $product->price * $item["quantity"];
            }

            // Assume single-seller cart for v1 (split-cart can be added later)
            $sellerId = $products->first()->seller_id;
            $deliveryFee = self::calculateDeliveryFee(
                $subtotal,
                $deliveryAddress["district"] ?? null,
            );

            $order = self::create([
                "buyer_id" => $buyer->id,
                "seller_id" => $sellerId,
                "status" => "pending",
                "subtotal" => $subtotal,
                "delivery_fee" => $deliveryFee,
                "total" => $subtotal + $deliveryFee,
                "payment_method" => $paymentMethod,
                "payment_status" => "pending",
                "delivery_district" => $deliveryAddress["district"] ?? null,
                "delivery_address" => $deliveryAddress["address"] ?? null,
                "delivery_lat" => $deliveryAddress["lat"] ?? null,
                "delivery_lng" => $deliveryAddress["lng"] ?? null,
            ]);

            foreach ($cartItems as $item) {
                $product = $products->get($item["product_id"]);

                $order->items()->create([
                    "product_id" => $product->id,
                    "product_name" => $product->name,
                    "unit_price" => $product->price,
                    "quantity" => $item["quantity"],
                    "total_price" => $product->price * $item["quantity"],
                    "unit" => $product->unit ?? "unit",
                ]);

                $product->reduceStock($item["quantity"]);
            }

            return $order->fresh("items");
        });
    }

    /**
     * Simple delivery fee calculator — flat rate per district distance band.
     * Replace with real distance-based calculation when GPS data available.
     */
    public static function calculateDeliveryFee(
        float $subtotal,
        ?string $district,
    ): float {
        if ($subtotal >= 5000) {
            return 0;
        } // free delivery above MWK 5,000

        return match ($district) {
            "Lilongwe", "Blantyre", "Mzuzu" => 50,
            default => 120,
        };
    }

    /**
     * Mark payment as received (called from mobile money webhook).
     */
    public function markAsPaid(string $reference): void
    {
        $this->update([
            "payment_status" => "paid",
            "payment_reference" => $reference,
            "paid_at" => now(),
        ]);

        if ($this->status === "pending") {
            $this->transitionTo("confirmed");
        }

        app(\App\Services\SmsService::class)->sendPaymentConfirmation($this);
    }
}
