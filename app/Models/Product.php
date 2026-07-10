<?php
// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "seller_id",
        "name",
        "slug",
        "description",
        "category",
        "status",
        "price",
        "unit",
        "currency",
        "price_negotiable",
        "stock_quantity",
        "minimum_order",
        "in_stock",
        "district",
        "region",
        "images",
        "thumbnail",
        "is_featured",
        "delivery_available",
    ];

    protected $casts = [
        "price" => "decimal:2",
        "price_negotiable" => "boolean",
        "in_stock" => "boolean",
        "images" => "array",
        "is_featured" => "boolean",
        "is_verified" => "boolean",
        "delivery_available" => "boolean",
        "average_rating" => "decimal:2",
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Product $p) {
            $p->slug = $p->slug ?: Str::slug($p->name) . "-" . Str::random(6);
        });
    }

    // ── Relationships ─────────────────────────────────────────────────

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, "seller_id");
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->where("is_approved", true);
    }

    public function wishlistedBy(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where("status", "active")->where("in_stock", true);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where("category", $category);
    }

    public function scopeDistrict($query, string $district)
    {
        return $query->where("district", $district);
    }

    public function scopePriceBetween($query, ?float $min, ?float $max)
    {
        if ($min !== null) {
            $query->where("price", ">=", $min);
        }
        if ($max !== null) {
            $query->where("price", "<=", $max);
        }
        return $query;
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) {
            return $query;
        }
        return $query->where(function ($q) use ($term) {
            $q->where("name", "like", "%{$term}%")->orWhere(
                "description",
                "like",
                "%{$term}%",
            );
        });
    }

    public function scopeFeatured($query)
    {
        return $query->where("is_featured", true);
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail ? asset("storage/" . $this->thumbnail) : null;
    }

    public function getFormattedPriceAttribute(): string
    {
        return "{$this->currency} " .
            number_format($this->price, 2) .
            "/{$this->unit}";
    }

    // ── Business Logic ────────────────────────────────────────────────

    /**
     * Check if the requested quantity is available.
     */
    public function hasStock(int $quantity): bool
    {
        return $this->in_stock &&
            $this->stock_quantity >= $quantity &&
            $quantity >= $this->minimum_order;
    }

    /**
     * Reduce stock after a successful order. Marks sold_out if it hits 0.
     *
     * @throws \Exception if insufficient stock
     */
    public function reduceStock(int $quantity): void
    {
        if (!$this->hasStock($quantity)) {
            throw new \Exception(
                "Insufficient stock for {$this->name}. Available: {$this->stock_quantity}",
            );
        }

        $this->decrement("stock_quantity", $quantity);
        $this->increment("total_sold", $quantity);

        if ($this->stock_quantity <= 0) {
            $this->update(["status" => "sold_out", "in_stock" => false]);
        }
    }

    /**
     * Restore stock — used when an order is cancelled.
     */
    public function restoreStock(int $quantity): void
    {
        $this->increment("stock_quantity", $quantity);
        $this->decrement("total_sold", min($quantity, $this->total_sold));

        if ($this->status === "sold_out" && $this->stock_quantity > 0) {
            $this->update(["status" => "active", "in_stock" => true]);
        }
    }

    /**
     * Recalculate and persist average rating.
     */
    public function recalculateRating(): void
    {
        $this->update([
            "average_rating" => round($this->reviews()->avg("rating") ?? 0, 2),
            "total_reviews" => $this->reviews()->count(),
        ]);
    }

    /**
     * Record a view (call on product detail page load).
     */
    public function recordView(): void
    {
        $this->increment("total_views");
    }
}
