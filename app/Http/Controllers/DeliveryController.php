<?php
// app/Http/Controllers/DeliveryController.php

namespace App\Http\Controllers;

use App\Events\DeliveryStatusUpdated;
use App\Events\DriverLocationUpdated;
use App\Jobs\BroadcastNotification;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Order;
use App\Models\TradingCentre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    /**
     * Buyer-facing delivery tracking dashboard — shows all active +
     * historical deliveries belonging to the logged-in user's orders.
     */
    public function index()
    {
        $activeDeliveries = Delivery::whereHas(
            "order",
            fn($q) => $q->where("buyer_id", Auth::id()),
        )
            ->whereNotIn("status", ["delivered", "failed"])
            ->with(["order.items", "driver.user", "trackingStickers"])
            ->latest()
            ->get();

        $deliveryHistory = Delivery::whereHas(
            "order",
            fn($q) => $q->where("buyer_id", Auth::id()),
        )
            ->whereIn("status", ["delivered", "failed"])
            ->with("order")
            ->latest()
            ->paginate(10);

        return view(
            "pages.delivery",
            compact("activeDeliveries", "deliveryHistory"),
        );
    }

    /**
     * Public order tracking by tracking number (no auth required — like
     * courier tracking pages). Shows live map + status timeline.
     */
    public function track(string $trackingNumber)
    {
        $delivery = Delivery::where("tracking_number", $trackingNumber)
            ->with(["order.items", "driver.user", "statusLogs"])
            ->first();

        if (!$delivery) {
            return view("pages.track", ["delivery" => null, "query" => $trackingNumber]);
        }

        // Load route waypoints (trading centres along the route)
        $waypoints = $delivery->getRouteWaypoints();
        $nearestCentre = $delivery->distanceToNearestCentre();
        $destCentre = $delivery->getDestinationTradingCentre();

        return view("pages.track", compact("delivery", "waypoints", "nearestCentre", "destCentre"));
    }

    /**
     * JSON endpoint polled every ~10s by the tracking page for live map updates.
     */
    public function liveStatus(Delivery $delivery)
    {
        $nearestCentre = $delivery->distanceToNearestCentre();
        $destCentre = $delivery->getDestinationTradingCentre();

        return response()->json([
            "status"                => $delivery->status,
            "progress_percentage"   => $delivery->progressPercentage(),
            "driver_lat"            => $delivery->driver_current_lat,
            "driver_lng"            => $delivery->driver_current_lng,
            "distance_remaining_km" => $delivery->distance_remaining_km,
            "estimated_arrival_at"  => $delivery->estimated_arrival_at?->toIso8601String(),
            "proximity_label"       => $delivery->getProximityLabel(),
            "nearest_centre"        => $nearestCentre,
            "destination_centre"    => $destCentre ? [
                'name'     => $destCentre->name,
                'lat'      => $destCentre->latitude,
                'lng'      => $destCentre->longitude,
                'district' => $destCentre->district?->name,
            ] : null,
            "origin_lat"            => $delivery->origin_lat,
            "origin_lng"            => $delivery->origin_lng,
            "destination_lat"       => $delivery->destination_lat,
            "destination_lng"       => $delivery->destination_lng,
        ]);
    }

    // ── Driver-facing actions ──────────────────────────────────────────

    /**
     * Driver app calls this every ~30 seconds with their current GPS coords.
     */
    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            "lat" => ["required", "numeric", "between:-90,90"],
            "lng" => ["required", "numeric", "between:-180,180"],
        ]);

        $driver = Auth::user()->driver()->firstOrFail();
        $driver->updateLocation($validated["lat"], $validated["lng"]);

        // Broadcast driver location to the buyer in real-time
        if ($activeDelivery = $driver->deliveries()->whereNotIn('status', ['delivered', 'failed'])->first()) {
            event(new DriverLocationUpdated($activeDelivery, $validated["lat"], $validated["lng"]));
        }

        return response()->json(["success" => true]);
    }

    /**
     * Driver advances a delivery through its workflow: collected → in_transit → delivered.
     */
    public function advanceStatus(
        Request $request,
        \App\Models\Delivery $delivery,
    ) {
        $statuses = [
            "pending",
            "assigned",
            "collected",
            "in_transit",
            "near_destination",
            "delivered",
        ];
        $current = array_search($delivery->status, $statuses);

        if ($current === false || $current >= count($statuses) - 1) {
            return response()->json([
                "success" => false,
                "message" => "Already at final status.",
            ]);
        }

        $nextStatus = $statuses[$current + 1];

        // Update the delivery
        $delivery->update(["status" => $nextStatus]);

        // Log the status change
        try {
            \App\Models\DeliveryStatusLog::create([
                "delivery_id" => $delivery->id,
                "status" => $nextStatus,
                "note" => $this->getStatusNote($nextStatus, $delivery),
                "logged_by" => Auth::id(),
            ]);
        } catch (\Throwable $e) {
        }

        // ── Send buyer notification for every status change ──────────
        $buyer = $delivery->order->buyer ?? null;
        if ($buyer) {
            $this->notifyBuyer($buyer, $delivery, $nextStatus);
        }

        // ── Broadcast delivery status update in real-time ───────────
        event(new DeliveryStatusUpdated($delivery, $nextStatus));

        return response()->json([
            "success" => true,
            "new_status" => $nextStatus,
            "message" =>
                "Delivery advanced to: " .
                ucwords(str_replace("_", " ", $nextStatus)),
        ]);
    }
    public function confirmDelivery(
        Request $request,
        \App\Models\Delivery $delivery,
    ) {
        abort_unless($delivery->order->buyer_id === Auth::id(), 403);

        $delivery->update([
            "status" => "delivered",
            "delivered_at" => now(),
        ]);

        $delivery->order->update(["status" => "delivered"]);

        // ── Thank-you notification ────────────────────────────────────
        try {
            BroadcastNotification::dispatch(
                Auth::id(),
                "🌾 Delivery Confirmed — Thank You!",
                "Thank you for confirming receipt of order {$delivery->order->order_number}!",
                "order",
                "fas fa-check-circle",
                "var(--primary)",
                route("marketplace.my-orders"),
            );
        } catch (\Throwable $e) {
        }

        // Broadcast delivery completed
        event(new DeliveryStatusUpdated($delivery, "delivered"));

        // Notify the seller about the delivery confirmation
        if ($sellerId = $delivery->order->seller_id) {
            try {
                BroadcastNotification::dispatch(
                    $sellerId,
                    "✅ Delivery Confirmed — " . $delivery->order->order_number,
                    "Buyer has confirmed receipt of order {$delivery->order->order_number}.",
                    "order",
                    "fas fa-check-circle",
                    "#16a34a",
                    route("marketplace.my-listings"),
                );
            } catch (\Throwable $e) {}
        }

        return back()->with(
            "success",
            "✅ Delivery confirmed! Thank you for your order.",
        );
    }
    /**
     * Admin assigns an available driver to a pending delivery.
     */
    public function assignDriver(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            "driver_id" => ["required", "exists:drivers,id"],
        ]);

        $driver = Driver::findOrFail($validated["driver_id"]);
        $driver->assignTo($delivery);

        return back()->with(
            "success",
            "Driver {$driver->user->full_name} assigned to delivery #{$delivery->tracking_number}.",
        );
    }

    /**
     * Buyer rates the delivery after it's marked delivered.
     */
    public function rate(Request $request, Delivery $delivery)
    {
        abort_unless($delivery->order->buyer_id === Auth::id(), 403);
        abort_unless(
            $delivery->status === "delivered",
            422,
            "Can only rate completed deliveries.",
        );

        $validated = $request->validate([
            "rating" => ["required", "integer", "min:1", "max:5"],
        ]);

        $delivery->update(["buyer_rating" => $validated["rating"]]);

        // Recalculate driver's running average rating
        if ($driver = $delivery->driver) {
            $avg = Delivery::where("driver_id", $driver->id)
                ->whereNotNull("buyer_rating")
                ->avg("buyer_rating");

            $driver->update([
                "average_rating" => round($avg, 2),
                "total_reviews" => Delivery::where("driver_id", $driver->id)
                    ->whereNotNull("buyer_rating")
                    ->count(),
                "total_deliveries" => Delivery::where("driver_id", $driver->id)
                    ->where("status", "delivered")
                    ->count(),
            ]);
        }

        return back()->with("success", "Thank you for your feedback!");
    }

    private function getStatusNote(
        string $status,
        \App\Models\Delivery $delivery,
    ): string {
        $orderNum = $delivery->order->order_number ?? "";
        return match ($status) {
            "assigned" => "Driver assigned to your order {$orderNum}.",
            "collected" => "Your items have been picked up from the seller.",
            "in_transit" => "Your order is on its way to you!",
            "near_destination"
                => "Almost there! Driver is about 1km from your location.",
            "delivered" => "Your order has been delivered. Thank you!",
            default => ucwords(str_replace("_", " ", $status)),
        };
    }

    private function notifyBuyer(
        \App\Models\User $buyer,
        \App\Models\Delivery $delivery,
        string $status,
    ): void {
        $orderNum = $delivery->order->order_number ?? "";
        $total = "MWK " . number_format($delivery->order->total ?? 0);
        $driver = $delivery->driver->user->full_name ?? "your driver";
        $plate = $delivery->driver->vehicle_plate ?? "";

        $messages = [
            "assigned" => [
                "title" => "🚗 Driver Assigned — " . $orderNum,
                "message" =>
                    "Good news! A driver has been assigned to your order {$orderNum}. " .
                    "Driver: {$driver}" .
                    ($plate ? " ({$plate})" : "") .
                    ". " .
                    "Your items will be picked up shortly.",
            ],
            "collected" => [
                "title" => "📦 Order Picked Up — " . $orderNum,
                "message" =>
                    "Your order {$orderNum} has been picked up from the seller and is heading your way. " .
                    "Track your delivery live on the Delivery page.",
            ],
            "in_transit" => [
                "title" => "🚚 Order On Its Way! — " . $orderNum,
                "message" =>
                    "Your order {$orderNum} is now in transit to you. " .
                    "Driver {$driver} is on the road. You can track the live location on the Delivery page.",
            ],
            "near_destination" => [
                "title" => "📍 Almost There! — " . $orderNum,
                "message" =>
                    "Your order is just about 1km away! Please be ready to receive it. " .
                    "Order: {$orderNum}. Total to pay on delivery: {$total}.",
            ],
            "delivered" => [
                "title" => "✅ Order Delivered — " . $orderNum,
                "message" =>
                    "Your order {$orderNum} has arrived! Please confirm receipt with the driver. " .
                    "We hope your farming products are exactly what you needed. " .
                    "Please rate your delivery experience. 🌾",
            ],
        ];

        if (!isset($messages[$status])) {
            return;
        }

        try {
            $actionUrl = $delivery->tracking_number
                ? route("delivery.track", $delivery->tracking_number)
                : route("delivery");

            BroadcastNotification::dispatch(
                $buyer->id,
                $messages[$status]["title"],
                $messages[$status]["message"],
                "order",
                "fas fa-truck",
                "var(--primary)",
                $actionUrl,
            );
        } catch (\Throwable $e) {
        }
    }
}
