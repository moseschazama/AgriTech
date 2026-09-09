<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Events\DiseaseAlertCreated;
use App\Events\OrderStatusChanged;
use App\Events\ProductApproved;
use App\Events\UserRegistered;
use App\Jobs\BroadcastNotification;
use App\Models\Notification;
use App\Models\District;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Course;
use App\Models\Innovation;
use App\Models\Competition;
use App\Models\DiseaseAlert;
use App\Models\SmsCampaign;
use App\Models\SmsLog;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * AdminController — covers all 8 panel tabs from admin.blade.php:
 * Analytics, Farmers, Products, Orders, Courses, Innovations, SMS, Settings.
 */
class AdminController extends Controller
{
    public function __construct(protected SmsService $sms)
    {
        // Share districts with ALL admin views
        view()->share('districts', District::orderBy('name')->get());
    }

    // ── Analytics Tab ──────────────────────────────────────────────────

    public function index()
    {
        $stats = [
            "total_farmers" => User::farmers()->count(),
            "total_products_sold" => Order::where(
                "status",
                "delivered",
            )->count(),
            "total_revenue" => Order::where("payment_status", "paid")->sum(
                "total",
            ),
            "total_deliveries" => \App\Models\Delivery::where(
                "status",
                "delivered",
            )->count(),
            "delivery_success_rate" => $this->deliverySuccessRate(),
        ];

        $districtBreakdown = User::farmers()
            ->select("district", DB::raw("count(*) as total"))
            ->whereNotNull("district")
            ->groupBy("district")
            ->orderByDesc("total")
            ->get();

        $recentActivity = $this->buildActivityFeed();

        return view(
            "pages.admin",
            compact("stats", "districtBreakdown", "recentActivity"),
        );
    }

    protected function deliverySuccessRate(): float
    {
        $total = \App\Models\Delivery::whereIn("status", [
            "delivered",
            "failed",
        ])->count();
        if ($total === 0) {
            return 0;
        }
        $success = \App\Models\Delivery::where("status", "delivered")->count();
        return round(($success / $total) * 100, 1);
    }

    /** Pull the 10 most recent platform events for the activity log table. */
    protected function buildActivityFeed(): \Illuminate\Support\Collection
    {
        $signups = User::latest()->limit(5)->get()->map(
            fn($u) => [
                "time" => $u->created_at,
                "event" => "New farmer registered",
                "user" => $u->full_name,
                "module" => "Auth",
                "status" => "success",
            ],
        );

        $orders = Order::latest()->limit(5)->get()->map(
            fn($o) => [
                "time" => $o->created_at,
                "event" => "Order #{$o->order_number} placed",
                "user" => $o->buyer->full_name,
                "module" => "Marketplace",
                "status" => "success",
            ],
        );

        return $signups
            ->concat($orders)
            ->sortByDesc("time")
            ->take(10)
            ->values();
    }

    // ── Farmers Tab ──────────────────────────────────────────────────

    public function farmers(Request $request)
    {
        $farmers = User::farmers()
            ->withCount(["orders", "enrollments"])
            ->when(
                $request->get("q"),
                fn($q, $term) => $q->where(
                    fn($w) => $w
                        ->where("first_name", "like", "%{$term}%")
                        ->orWhere("last_name", "like", "%{$term}%")
                        ->orWhere("phone", "like", "%{$term}%"),
                ),
            )
            ->when($request->get("district"), fn($q, $p) => $q->byDistrict($p))
            ->when(
                $request->get("status"),
                fn($q, $s) => $q->where("status", $s),
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view("pages.admin", compact("farmers"));
    }

    public function suspendFarmer(User $user)
    {
        $user->update(["status" => "suspended"]);

        // Notify the user
        try {
            BroadcastNotification::dispatch(
                $user->id,
                "⚠️ Account Suspended",
                "Your AgriTech Pro account has been suspended by an administrator. Please contact support for assistance.",
                "account",
                "fas fa-ban",
                "#ef4444",
                route("dashboard"),
            );
        } catch (\Throwable $e) {}

        return back()->with(
            "success",
            "{$user->full_name} has been suspended.",
        );
    }

    public function activateFarmer(User $user)
    {
        $user->update(["status" => "active"]);

        // Notify the user
        try {
            BroadcastNotification::dispatch(
                $user->id,
                "✅ Account Reactivated",
                "Your AgriTech Pro account has been reactivated. You can now access all features again.",
                "account",
                "fas fa-check-circle",
                "#16a34a",
                route("dashboard"),
            );
        } catch (\Throwable $e) {}

        return back()->with(
            "success",
            "{$user->full_name} has been reactivated.",
        );
    }

    public function farmerDetail(User $user)
    {
        $user->load(["farm", "orders" => fn($q) => $q->latest()->limit(5)]);
        return response()->json([
            "id" => $user->id,
            "full_name" => $user->full_name,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "email" => $user->email,
            "phone" => $user->phone,
            "role" => $user->role,
            "status" => $user->status,
            "district" => $user->district,
            "trading_centre" => $user->trading_centre,
            "village" => $user->village,
            "farm_type" => $user->farm?->farm_type,
            "registered_at" => $user->created_at->format("M j, Y"),
            "last_login" => $user->last_login_at?->diffForHumans(),
            "recent_orders" => $user->orders->map(
                fn($o) => [
                    "order_number" => $o->order_number,
                    "total" => $o->total,
                    "status" => $o->status,
                    "created_at" => $o->created_at->format("M j, Y"),
                ],
            ),
        ]);
    }

    public function resetFarmerPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            "new_password" => ["required", "string", "min:8"],
        ]);

        $user->update([
            "password" => Hash::make($validated["new_password"]),
        ]);

        // Notify the user via SMS about the password change
        try {
            $this->sms->send(
                phone: $user->phone,
                message:
                    "Hello {$user->first_name}, your AgriTech Pro account password was reset by an admin. Your new password is: {$validated["new_password"]}. Please log in and change it. — AgriTech Pro Team",
                type: "custom",
                recipient: $user,
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning(
                "Password reset SMS failed for user {$user->id}: " .
                    $e->getMessage(),
            );
        }

        return back()->with(
            "success",
            "Password for {$user->full_name} has been reset. New password sent via SMS.",
        );
    }

    // ── Products Tab ───────────────────────────────────────────────────

    public function products(Request $request)
    {
        $products = Product::with("seller")
            ->when($request->get("q"), fn($q, $term) => $q->search($term))
            ->when(
                $request->get("status"),
                fn($q, $s) => $q->where("status", $s),
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view("pages.admin", compact("products"));
    }

    /** Approve a pending product listing so it goes live in the marketplace. */
    public function approveProduct(Product $product)
    {
        $product->update(["status" => "active", "is_verified" => true]);

        // Notify the seller
        try {
            BroadcastNotification::dispatch(
                $product->seller_id,
                "✅ Listing Approved — " . $product->name,
                "Your product is now live in the marketplace!",
                "marketplace",
                "fas fa-check-circle",
                "#16a34a",
                route("marketplace.show", $product->slug),
            );
        } catch (\Throwable $e) {}

        // Broadcast product approved event
        event(new ProductApproved($product));

        return back()->with(
            "success",
            "\"{$product->name}\" approved and is now live.",
        );
    }

    public function rejectProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            "reason" => ["required", "string", "max:500"],
        ]);

        $product->update([
            "status" => "inactive",
            "rejection_reason" => $validated["reason"],
        ]);

        // Notify the seller
        try {
            BroadcastNotification::dispatch(
                $product->seller_id,
                "❌ Listing Rejected — " . $product->name,
                "Your product was not approved. Reason: {$validated['reason']}",
                "marketplace",
                "fas fa-times-circle",
                "#ef4444",
                route("marketplace.my-listings"),
            );
        } catch (\Throwable $e) {}

        return back()->with("success", "\"{$product->name}\" rejected.");
    }
    // ── Orders Tab ───────────────────────────────────────────────────

    public function orders(Request $request)
    {
        $orders = Order::with(["buyer", "seller"])
            ->when($request->get("status"), fn($q, $s) => $q->status($s))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view("pages.admin", compact("orders"));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate(["status" => ["required", "string"]]);

        $oldStatus = $order->status;

        try {
            $order->transitionTo($validated["status"]);
        } catch (\Exception $e) {
            return back()->withErrors(["status" => $e->getMessage()]);
        }

        // Broadcast order status change
        event(new OrderStatusChanged($order, $oldStatus));

        // Notify buyer
        try {
            $label = Order::STATUS_LABELS[$validated["status"]] ?? ucfirst($validated["status"]);
            BroadcastNotification::dispatch(
                $order->buyer_id,
                "📦 Order Update — " . $order->order_number,
                "Your order status: {$label}",
                "order",
                "fas fa-truck",
                "var(--primary)",
                route("marketplace.my-orders"),
            );
        } catch (\Throwable $e) {}

        $label = Order::STATUS_LABELS[$validated["status"]] ?? ucfirst($validated["status"]);
        return back()->with(
            "success",
            "Order #{$order->order_number} updated to {$label}.",
        );
    }

    /**
     * Admin dispatches an order — assigns driver, enters delivery details,
     * creates delivery record if missing, sends SMS + notification to buyer.
     */
    public function dispatchOrder(Request $request, Order $order)
    {
        $validated = $request->validate([
            "driver_name" => ["required", "string", "max:150"],
            "driver_phone" => ["required", "string", "max:20"],
            "driver_vehicle_plate" => ["required", "string", "max:20"],
            "driver_vehicle_type" => ["nullable", "string", "max:100"],
            "delivery_notes" => ["nullable", "string", "max:500"],
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Ensure delivery record exists
            $delivery = $order->delivery;
            if (!$delivery) {
                $order->resolveDeliveryCoordinates();
                $order->refresh();
                $originDistrict = $order->seller?->district ?? "Lilongwe";
                $originCoords = \App\Models\Order::resolveOriginCoordinates($originDistrict);
                $delivery = \App\Models\Delivery::create([
                    "order_id" => $order->id,
                    "tracking_number" => "TRK-" . now()->format("Ymd") . "-" . strtoupper(\Illuminate\Support\Str::random(6)),
                    "status" => "pending",
                    "origin_address" => \App\Models\Order::originAddressFor($order->seller, $originDistrict),
                    "origin_district" => $originDistrict,
                    "origin_lat" => $originCoords['origin_lat'] ?? null,
                    "origin_lng" => $originCoords['origin_lng'] ?? null,
                    "destination_district" => $order->delivery_district,
                    "destination_address" => $order->delivery_address,
                    "destination_lat" => $order->delivery_lat,
                    "destination_lng" => $order->delivery_lng,
                ]);
                $order->setRelation("delivery", $delivery);
            } else {
                $updates = [];
                if (!$delivery->destination_lat && $order->delivery_lat) {
                    $updates['destination_lat'] = $order->delivery_lat;
                    $updates['destination_lng'] = $order->delivery_lng;
                }
                if (!$delivery->origin_lat) {
                    $originCoords = \App\Models\Order::resolveOriginCoordinates($delivery->origin_district ?? $order->seller?->district);
                    if ($originCoords) {
                        $updates['origin_lat'] = $originCoords['origin_lat'];
                        $updates['origin_lng'] = $originCoords['origin_lng'];
                    }
                }
                if (!empty($updates)) { $delivery->update($updates); }
            }

            // Store driver details directly on the delivery record
            $delivery->update([
                "driver_name" => $validated["driver_name"],
                "driver_phone" => $validated["driver_phone"],
                "driver_vehicle_plate" => strtoupper($validated["driver_vehicle_plate"]),
                "driver_vehicle_type" => $validated["driver_vehicle_type"] ?? null,
                "status" => "assigned",
                "assigned_at" => now(),
                "delivery_notes" => $validated["delivery_notes"]
                    ? ($delivery->delivery_notes
                        ? $delivery->delivery_notes . "\n---\n" . $validated["delivery_notes"]
                        : $validated["delivery_notes"])
                    : $delivery->delivery_notes,
            ]);

            // Log the assignment
            $delivery->logStatus("assigned", "Driver {$validated['driver_name']} ({$validated['driver_vehicle_plate']}) assigned by admin.");

            // Transition order to on_the_way
            $order->transitionTo("on_the_way");

            // Confirm payment status
            if ($order->payment_status === "pending") {
                $order->update(["payment_status" => "confirmed"]);
            }

            \Illuminate\Support\Facades\DB::commit();

            // Broadcast delivery status update in real-time
            event(new \App\Events\DeliveryStatusUpdated($delivery, "assigned"));

            // Send SMS to buyer with driver details
            try {
                $buyer = $order->buyer;
                if ($buyer) {
                    $msg = "Your order {$order->order_number} is on the way! "
                         . "Driver: {$validated['driver_name']} ({$validated['driver_phone']}) "
                         . "Vehicle: {$validated['driver_vehicle_plate']} "
                         . ($validated['driver_vehicle_type'] ? "({$validated['driver_vehicle_type']}) " : "")
                         . "Track live: " . url("/track/{$delivery->tracking_number}")
                         . " — AgriTech Pro";
                    app(\App\Services\SmsService::class)->send(
                        phone: $buyer->phone,
                        message: $msg,
                        type: "order",
                        recipient: $buyer,
                    );
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Dispatch SMS failed for order {$order->id}: " . $e->getMessage());
            }

            $label = \App\Models\Order::STATUS_LABELS["on_the_way"] ?? "On the Way";
            return back()->with(
                "success",
                "Order #{$order->order_number} dispatched to {$validated['driver_name']}. {$label}. SMS sent to buyer.",
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withErrors(["dispatch" => "Dispatch failed: " . $e->getMessage()]);
        }
    }

    // ── Courses Tab ────────────────────────────────────────────────────

    public function courses()
    {
        $courses = Course::with("instructor")->latest()->paginate(10);
        return view("pages.admin", compact("courses"));
    }

    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            "title" => ["required", "string", "max:150"],
            "category" => [
                "required",
                "in:soil_crops,livestock,agri_tech,agribusiness,organic,irrigation,post_harvest",
            ],
            "instructor_name" => ["required", "string", "max:150"],
            "price" => ["required", "numeric", "min:0"],
            "description" => ["required", "string"],
            "thumbnail" => ["nullable", "image", "max:4096"],
        ]);

        // Find an existing instructor by name, or create one on the fly —
        // this is what the original form's placeholder text promised but
        // the old validation rule never actually allowed to happen.
        $instructor = \App\Models\Instructor::firstOrCreate(
            ["name" => $validated["instructor_name"]],
            ["is_active" => true],
        );

        $thumbnailPath = $request->hasFile("thumbnail")
            ? $request->file("thumbnail")->store("courses/thumbnails", "public")
            : null;

        $course = Course::create([
            "title" => $validated["title"],
            "category" => $validated["category"],
            "instructor_id" => $instructor->id,
            "price" => $validated["price"],
            "description" => $validated["description"],
            "access_type" => $validated["price"] > 0 ? "paid" : "free",
            "status" => "draft",
            "thumbnail" => $thumbnailPath,
        ]);

        return redirect()
            ->route("admin.courses")
            ->with(
                "success",
                "\"{$course->title}\" created as draft. Add at least one lesson below, then publish it.",
            );
    }

    public function storeLesson(Request $request, Course $course)
    {
        $validated = $request->validate([
            "title" => ["required", "string", "max:150"],
            "type" => ["required", "in:video,pdf,quiz,text"],
            "video_url" => ["nullable", "url", "max:500"],
            "pdf_file" => ["nullable", "file", "mimes:pdf", "max:20480"],
            "duration_minutes" => ["required", "integer", "min:1", "max:600"],
            "is_free_preview" => ["nullable"],
            "is_published" => ["nullable"],
        ]);

        // Store uploaded PDF if provided
        $pdfPath = $request->hasFile("pdf_file")
            ? $request->file("pdf_file")->store("lessons/pdfs", "public")
            : null;

        // Determine sort order (append at end)
        $nextOrder = ($course->lessons()->max("sort_order") ?? 0) + 1;

        $lesson = $course->lessons()->create([
            "title" => $validated["title"],
            "type" => $validated["type"],
            "video_url" => $validated["video_url"] ?? null,
            "pdf_path" => $pdfPath,
            "duration_minutes" => $validated["duration_minutes"],
            "sort_order" => $nextOrder,
            "is_free_preview" => $request->boolean("is_free_preview", false),
            "is_published" => $request->boolean("is_published", true),
        ]);

        // ── EXPLICIT recalculation ──────────────────────────────────────
        // Do NOT rely solely on the Lesson model's `saved` event.
        // Call recalculateStats() directly so total_lessons and
        // total_duration_minutes in the courses table are guaranteed
        // to reflect the new lesson immediately.
        $course->recalculateStats();

        $totalNow = $course->fresh()->total_lessons;

        return back()->with(
            "success",
            "✅ Lesson \"{$lesson->title}\" added to \"{$course->title}\". " .
                "({$totalNow} lesson" .
                ($totalNow === 1 ? "" : "s") .
                " total)" .
                ($course->status === "draft" && $totalNow >= 1
                    ? " Course is now ready to publish!"
                    : ""),
        );
    }
    public function publishCourse(Course $course)
    {
        abort_if(
            $course->lessons()->count() === 0,
            422,
            "Add at least one lesson before publishing.",
        );
        $course->update(["status" => "published"]);
        return back()->with(
            "success",
            "\"{$course->title}\" is now published.",
        );
    }
    public function destroyLesson(\App\Models\Lesson $lesson)
    {
        $courseTitle = $lesson->course->title;
        $lesson->delete();

        return back()->with(
            "success",
            "Lesson removed from \"{$courseTitle}\".",
        );
    }
    public function reviews()
    {
        $pendingReviews = \App\Models\CourseReview::with(["user", "course"])
            ->where("is_approved", false)
            ->latest()
            ->paginate(10);

        return view("pages.admin", compact("pendingReviews"));
    }

    public function approveReview(\App\Models\CourseReview $review)
    {
        $review->update(["is_approved" => true]);

        return back()->with(
            "success",
            "Review approved and is now visible on the course page.",
        );
    }

    public function rejectReview(\App\Models\CourseReview $review)
    {
        $courseTitle = $review->course->title;
        $review->delete();

        return back()->with(
            "success",
            "Review for \"{$courseTitle}\" rejected and removed.",
        );
    }

    // ── Innovations Tab ──────────────────────────────────────────────

    public function innovations()
    {
        $innovations = Innovation::with("user")->latest()->paginate(10);
        return view("pages.admin", compact("innovations"));
    }

    public function approveInnovation(Request $request, Innovation $innovation)
    {
        $innovation->approve($request->user());

        // Notify the innovator
        try {
            BroadcastNotification::dispatch(
                $innovation->user_id,
                "✅ Innovation Approved — " . $innovation->title,
                "Your innovation is now live in the Innovation Hub!",
                "innovation",
                "fas fa-check-circle",
                "#16a34a",
                route("innovation.show", $innovation->slug),
            );
        } catch (\Throwable $e) {}

        return back()->with("success", "\"{$innovation->title}\" approved.");
    }

    public function rejectInnovation(Request $request, Innovation $innovation)
    {
        $validated = $request->validate([
            "reason" => ["required", "string", "max:500"],
        ]);
        $innovation->reject($request->user(), $validated["reason"]);

        // Notify the innovator
        try {
            BroadcastNotification::dispatch(
                $innovation->user_id,
                "❌ Innovation Rejected — " . $innovation->title,
                "Reason: {$validated['reason']}",
                "innovation",
                "fas fa-times-circle",
                "#ef4444",
                route("innovation.my-innovations"),
            );
        } catch (\Throwable $e) {}

        return back()->with("success", "\"{$innovation->title}\" rejected.");
    }

    /**
     * Rank the current competition entries by votes, crown 1st/2nd/3rd and close the round.
     */
    public function selectCompetitionWinners(Request $request, Competition $competition)
    {
        abort_unless(in_array($competition->status, ["active", "closed"]), 404);

        if ($competition->winners()->count() > 0) {
            return back()->with(
                "success",
                "Winners for " . $competition->title . " were already selected.",
            );
        }

        $winners = $competition->selectWinners();

        if (count($winners) === 0) {
            return back()->with("error", "No eligible entries to rank yet.");
        }

        try {
            BroadcastNotification::dispatch(
                $winners[0]["user_id"],
                "🏆 " . $competition->title . " — Results Published",
                "The leaderboard is live. Download the full results list from the Innovation Hub.",
                "innovation",
                "fas fa-trophy",
                "#f59e0b",
                route("innovation"),
            );
        } catch (\Throwable $e) {}

        return back()->with(
            "success",
            "Winners selected: #1 " . ($winners[0]["title"] ?? "") . ". The round is now closed.",
        );
    }

    /**
     * Download the competition entries list as CSV (with winner ranking when published).
     */
    public function downloadCompetitionEntries(Competition $competition)
    {
        $rows = $competition->entriesWithPerformance();

        $csv = fopen("php://temp/maxmemory:1048576", "rw");
        fputcsv($csv, [
            "Position",
            "Prize (MWK)",
            "Innovation",
            "Category",
            "Farmer",
            "District",
            "Votes",
            "Views",
            "Impact",
            "Submitted",
        ]);

        $rows->each(function ($row) use ($csv) {
            fputcsv($csv, [
                $row["position"] ?: "",
                $row["position"] && $row["prize"] !== null
                    ? number_format((float) $row["prize"])
                    : "",
                $row["title"],
                $row["category"],
                $row["farmer"],
                $row["district"],
                $row["votes"],
                $row["views"],
                $row["impact"],
                $row["submitted_at"],
            ]);
        });

        rewind($csv);
        $output = stream_get_contents($csv);
        fclose($csv);

        return response($output, 200, [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" =>
                'attachment; filename="' . Str::slug($competition->title) . '-entries.csv"',
        ]);
    }

    // ── SMS Tab ──────────────────────────────────────────────────────

    public function sms()
    {
        $stats = [
            "sent_this_month" => SmsLog::whereMonth(
                "created_at",
                now()->month,
            )->count(),
            "delivery_rate" => $this->smsDeliveryRate(),
            "cost_this_month" => SmsLog::whereMonth(
                "created_at",
                now()->month,
            )->sum("cost"),
        ];

        $recentCampaigns = SmsCampaign::latest()->limit(5)->get();

        $districts = District::with("tradingCentres")->orderBy("name")->get();

        return view("pages.admin", compact("stats", "recentCampaigns"));
    }

    protected function smsDeliveryRate(): float
    {
        $total = SmsLog::whereIn("status", ["delivered", "failed"])->count();
        if ($total === 0) {
            return 0;
        }
        return round(
            (SmsLog::where("status", "delivered")->count() / $total) * 100,
            1,
        );
    }

    /**
     * Create + immediately execute an SMS broadcast campaign.
     */
    public function sendSmsBroadcast(Request $request)
    {
        $validated = $request->validate([
            "name" => ["required", "string", "max:150"],
            "message" => ["required", "string", "max:480"], // ~3 SMS segments
            "type" => ["required", "string"],
            "target_districts" => ["nullable", "array"],
            "target_roles" => ["nullable", "array"],
            "schedule_for" => ["nullable", "date", "after:now"],
        ]);

        $campaign = SmsCampaign::create([
            ...$validated,
            "created_by" => $request->user()->id,
            "status" =>
                $validated["schedule_for"] ?? null ? "scheduled" : "draft",
            "scheduled_for" => $validated["schedule_for"] ?? null,
        ]);

        // Send immediately unless scheduled for later (a queued job picks up scheduled ones)
        if (!$campaign->scheduled_for) {
            $this->sms->executeCampaign($campaign);
        }

        return back()->with(
            "success",
            "Campaign \"{$campaign->name}\" " .
                ($campaign->scheduled_for
                    ? "scheduled."
                    : "sent to {$campaign->fresh()->total_sent} farmers."),
        );
    }

    /**
     * Create a disease alert and immediately broadcast it via SMS.
     */
    public function sendDiseaseAlert(Request $request)
    {
        $validated = $request->validate([
            "title" => ["required", "string", "max:150"],
            "description" => ["required", "string"],
            "alert_type" => ["required", "in:critical,warning,info"],
            "affected_districts" => ["required", "array", "min:1"],
            "recommended_action" => ["nullable", "string"],
            "disease_id" => ["nullable", "exists:diseases,id"],
        ]);

        $alert = DiseaseAlert::create([
            ...$validated,
            "created_by" => $request->user()->id,
            "is_active" => true,
        ]);

        $notified = $alert->broadcastViaSms();

        // Broadcast disease alert to all connected users
        event(new DiseaseAlertCreated($alert));

        return back()->with(
            "success",
            "Alert sent to {$notified} farmers via SMS.",
        );
    }

    // ── Settings Tab ───────────────────────────────────────────────────

    public function settings()
    {
        $settings = [
            "maintenance_mode" => \App\Models\Setting::get(
                "maintenance_mode",
                false,
            ),
            "allow_registrations" => \App\Models\Setting::get(
                "allow_registrations",
                true,
            ),
            "sms_notifications" => \App\Models\Setting::get(
                "sms_notifications",
                true,
            ),
            "disease_alerts_active" => \App\Models\Setting::get(
                "disease_alerts_active",
                true,
            ),
            "marketplace_escrow" => \App\Models\Setting::get(
                "marketplace_escrow",
                true,
            ),
        ];

        return view("pages.admin", compact("settings"));
    }

    public function updateSettings(Request $request)
    {
        foreach (
            [
                "maintenance_mode",
                "allow_registrations",
                "sms_notifications",
                "disease_alerts_active",
                "marketplace_escrow",
            ]
            as $key
        ) {
            \App\Models\Setting::set($key, $request->boolean($key));
        }

        return back()->with("success", "Settings saved.");
    }
}
