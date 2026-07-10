<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Course;
use App\Models\Innovation;
use App\Models\DiseaseAlert;
use App\Models\SmsCampaign;
use App\Models\SmsLog;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return back()->with(
            "success",
            "{$user->full_name} has been suspended.",
        );
    }

    public function activateFarmer(User $user)
    {
        $user->update(["status" => "active"]);
        return back()->with(
            "success",
            "{$user->full_name} has been reactivated.",
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

        try {
            $order->transitionTo($validated["status"]);
        } catch (\Exception $e) {
            return back()->withErrors(["status" => $e->getMessage()]);
        }

        return back()->with(
            "success",
            "Order #{$order->order_number} updated to {$validated["status"]}.",
        );
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
        return back()->with("success", "\"{$innovation->title}\" approved.");
    }

    public function rejectInnovation(Request $request, Innovation $innovation)
    {
        $validated = $request->validate([
            "reason" => ["required", "string", "max:500"],
        ]);
        $innovation->reject($request->user(), $validated["reason"]);
        return back()->with("success", "\"{$innovation->title}\" rejected.");
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
