<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LessonPdfController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LearnController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\InnovationController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\GuideController;

/*
|--------------------------------------------------------------------------
| Public Routes — no authentication required
|--------------------------------------------------------------------------
*/

Route::get("/", [HomeController::class, "index"])->name("home");

// Authentication
Route::get("/login", [AuthController::class, "showLogin"])->name("login");
Route::get("/register", [AuthController::class, "showRegister"])->name(
    "register",
);
Route::get("/lesson-pdf/{path}", [LessonPdfController::class, "serve"])
    ->name("lesson.pdf")
    ->where("path", ".+");
Route::post("/login", [AuthController::class, "login"])->name("login.submit");
Route::post("/register", [AuthController::class, "register"])->name(
    "register.submit",
);
Route::post("/logout", [AuthController::class, "logout"])
    ->name("logout")
    ->middleware("auth");

Route::post("/forgot-password", [AuthController::class, "sendResetOtp"])->name(
    "password.send-otp",
);
Route::post("/reset-password/verify", [
    AuthController::class,
    "verifyResetOtp",
])->name("password.verify-otp");

// Marketplace (browsing is public; selling/buying requires auth)
Route::get("/marketplace", [MarketplaceController::class, "index"])->name(
    "marketplace",
);
Route::get("/marketplace/{product:slug}", [
    MarketplaceController::class,
    "show",
])->name("marketplace.show");

// Learning Center (browsing public; enrolling requires auth)
Route::get("/learn", [LearnController::class, "index"])->name("learn");
Route::get("/learn/{course:slug}", [LearnController::class, "show"])->name(
    "learn.show",
);

// Innovation Hub (browsing public; submitting/voting requires auth)
Route::get("/innovation", [InnovationController::class, "index"])->name(
    "innovation",
);
Route::get("/innovation/{innovation:slug}", [
    InnovationController::class,
    "show",
])->name("innovation.show");

// Disease Detection & Library
Route::get("/diseases", [DiseaseController::class, "index"])->name("diseases");
Route::get("/diseases/{disease:slug}", [
    DiseaseController::class,
    "show",
])->name("diseases.show");
Route::post("/diseases/detect", [DiseaseController::class, "detect"])
    ->name("diseases.detect")
    ->middleware("auth");
Route::post("/diseases/subscribe", [
    DiseaseController::class,
    "subscribeAlerts",
])->name("diseases.subscribe");

// Public delivery tracking
Route::get("/track/{trackingNumber}", [
    DeliveryController::class,
    "track",
])->name("delivery.track");
Route::get("/track/{delivery}/live-status", [
    DeliveryController::class,
    "liveStatus",
])->name("delivery.live-status");

// PDF Guides — public download (no login needed, farmers in the field can access)
Route::get("/guides/{guide}/download", [
    GuideController::class,
    "download",
])->name("guides.download");

/*
|--------------------------------------------------------------------------
| Authenticated Routes — require login
|--------------------------------------------------------------------------
*/

Route::middleware("auth")->group(function () {
    // Dashboard & Profile
    Route::get("/dashboard", [DashboardController::class, "index"])->name(
        "dashboard",
    );
    Route::get("/profile", [ProfileController::class, "index"])->name(
        "profile",
    );

    Route::post("/profile/personal-info", [
        ProfileController::class,
        "updatePersonalInfo",
    ])->name("profile.personal-info");
    Route::post("/profile/notifications", [
        ProfileController::class,
        "updateNotificationPreferences",
    ])->name("profile.notifications");
    Route::post("/profile/farm", [
        ProfileController::class,
        "updateFarm",
    ])->name("profile.farm");
    Route::post("/profile/farm/production", [
        ProfileController::class,
        "addProductionRecord",
    ])->name("profile.farm.production");
    Route::post("/profile/password", [
        ProfileController::class,
        "updatePassword",
    ])->name("profile.password");
    Route::post("/profile/2fa", [
        ProfileController::class,
        "toggleTwoFactor",
    ])->name("profile.2fa");

    // Notifications
    Route::get("/notifications", [
        NotificationController::class,
        "index",
    ])->name("notifications");
    Route::get("/notifications/recent", [
        NotificationController::class,
        "recent",
    ])->name("notifications.recent");
    Route::post("/notifications/{notification}/read", [
        NotificationController::class,
        "markRead",
    ])->name("notifications.read");
    Route::post("/notifications/mark-all-read", [
        NotificationController::class,
        "markAllRead",
    ])->name("notifications.mark-all-read");

    // ── Marketplace: selling, cart, checkout ─────────────────────────
    Route::post("/marketplace/products", [
        MarketplaceController::class,
        "store",
    ])->name("marketplace.store");
    Route::put("/marketplace/products/{product}", [
        MarketplaceController::class,
        "update",
    ])->name("marketplace.update");
    Route::delete("/marketplace/products/{product}", [
        MarketplaceController::class,
        "destroy",
    ])->name("marketplace.destroy");
    Route::post("/marketplace/products/{product}/wishlist", [
        MarketplaceController::class,
        "toggleWishlist",
    ])->name("marketplace.wishlist");

    Route::get("/cart", [MarketplaceController::class, "viewCart"])->name(
        "cart",
    );
    Route::post("/cart/{product}", [
        MarketplaceController::class,
        "addToCart",
    ])->name("cart.add");
    Route::patch("/cart/{product}", [
        MarketplaceController::class,
        "updateCartItem",
    ])->name("cart.update");
    Route::post("/checkout", [MarketplaceController::class, "checkout"])->name(
        "checkout",
    );

    Route::get("/orders/{order}/confirmation", [
        MarketplaceController::class,
        "orderConfirmation",
    ])->name("marketplace.order.confirmation");
    Route::get("/my-orders", [MarketplaceController::class, "myOrders"])->name(
        "marketplace.my-orders",
    );
    Route::post("/delivery/{delivery}/confirm", [
        DeliveryController::class,
        "confirmDelivery",
    ])->name("delivery.confirm");
    Route::get("/my-listings", [
        MarketplaceController::class,
        "myListings",
    ])->name("marketplace.my-listings");

    // ── Learning Center: enroll, progress, reviews ───────────────────
    Route::post("/learn/{course:slug}/enroll", [
        LearnController::class,
        "enroll",
    ])->name("learn.enroll");
    Route::post("/learn/lessons/{lesson}/complete", [
        LearnController::class,
        "completeLesson",
    ])->name("learn.lesson.complete");
    Route::post("/learn/{course:slug}/review", [
        LearnController::class,
        "submitReview",
    ])->name("learn.review");
    Route::get("/my-courses", [LearnController::class, "myCourses"])->name(
        "learn.my-courses",
    );
    Route::get("/certificates/{enrollment}", [
        LearnController::class,
        "certificate",
    ])->name("learn.certificate");

    // ── Innovation Hub: submit, vote ─────────────────────────────────
    Route::post("/innovation", [InnovationController::class, "store"])->name(
        "innovation.store",
    );
    Route::post("/innovation/{innovation}/vote", [
        InnovationController::class,
        "vote",
    ])->name("innovation.vote");
    Route::get("/my-innovations", [
        InnovationController::class,
        "myInnovations",
    ])->name("innovation.mine");

    // ── Delivery: buyer dashboard + ratings ──────────────────────────
    Route::get("/delivery", [DeliveryController::class, "index"])->name(
        "delivery",
    );
    Route::post("/delivery/{delivery}/rate", [
        DeliveryController::class,
        "rate",
    ])->name("delivery.rate");

    // ── Disease: scan history + feedback ─────────────────────────────
    Route::get("/my-detections", [
        DiseaseController::class,
        "myDetections",
    ])->name("diseases.mine");
    Route::post("/diseases/detections/{detection}/feedback", [
        DiseaseController::class,
        "feedback",
    ])->name("diseases.feedback");
});

/*
|--------------------------------------------------------------------------
| Driver Routes — for the delivery driver mobile app
|--------------------------------------------------------------------------
*/

Route::middleware(["auth", "role:driver"])
    ->prefix("driver")
    ->group(function () {
        Route::post("/location", [
            DeliveryController::class,
            "updateLocation",
        ])->name("driver.location");
        Route::post("/deliveries/{delivery}/advance", [
            DeliveryController::class,
            "advanceStatus",
        ])->name("driver.delivery.advance");
    });

/*
|--------------------------------------------------------------------------
| Admin Routes — require role:admin middleware
|--------------------------------------------------------------------------
*/

Route::middleware(["auth", "role:admin"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::get("/", [AdminController::class, "index"])->name("index");

        // ── Farmers tab ───────────────────────────────────────────────
        Route::get("/farmers", [AdminController::class, "farmers"])->name(
            "farmers",
        );
        Route::post("/farmers/{user}/suspend", [
            AdminController::class,
            "suspendFarmer",
        ])->name("farmers.suspend");
        Route::post("/farmers/{user}/activate", [
            AdminController::class,
            "activateFarmer",
        ])->name("farmers.activate");

        // ── Products tab ──────────────────────────────────────────────
        Route::get("/products", [AdminController::class, "products"])->name(
            "products",
        );
        Route::post("/products/{product}/approve", [
            AdminController::class,
            "approveProduct",
        ])->name("products.approve");
        Route::post("/products/{product}/reject", [
            AdminController::class,
            "rejectProduct",
        ])->name("products.reject");

        // ── Orders tab ────────────────────────────────────────────────
        Route::get("/orders", [AdminController::class, "orders"])->name(
            "orders",
        );
        Route::post("/orders/{order}/status", [
            AdminController::class,
            "updateOrderStatus",
        ])->name("orders.status");
        Route::post("/deliveries/{delivery}/assign-driver", [
            DeliveryController::class,
            "assignDriver",
        ])->name("deliveries.assign-driver");

        // ── Courses tab ───────────────────────────────────────────────
        Route::get("/courses", [AdminController::class, "courses"])->name(
            "courses",
        );
        Route::post("/courses", [AdminController::class, "storeCourse"])->name(
            "courses.store",
        );
        Route::post("/courses/{course}/publish", [
            AdminController::class,
            "publishCourse",
        ])->name("courses.publish");

        // Lesson management (fixes Critical Bug #2 — courses were permanently stuck as drafts)
        Route::post("/courses/{course}/lessons", [
            AdminController::class,
            "storeLesson",
        ])->name("courses.lessons.store");
        Route::delete("/lessons/{lesson}", [
            AdminController::class,
            "destroyLesson",
        ])->name("lessons.destroy");

        // ── Reviews tab (new — fixes unused is_approved column) ───────
        Route::get("/reviews", [AdminController::class, "reviews"])->name(
            "reviews",
        );
        Route::post("/reviews/{review}/approve", [
            AdminController::class,
            "approveReview",
        ])->name("reviews.approve");
        Route::delete("/reviews/{review}", [
            AdminController::class,
            "rejectReview",
        ])->name("reviews.reject");

        // ── Innovations tab ───────────────────────────────────────────
        Route::get("/innovations", [
            AdminController::class,
            "innovations",
        ])->name("innovations");
        Route::post("/innovations/{innovation}/approve", [
            AdminController::class,
            "approveInnovation",
        ])->name("innovations.approve");
        Route::post("/innovations/{innovation}/reject", [
            AdminController::class,
            "rejectInnovation",
        ])->name("innovations.reject");

        // ── SMS tab ───────────────────────────────────────────────────
        Route::get("/sms", [AdminController::class, "sms"])->name("sms");
        Route::post("/sms/broadcast", [
            AdminController::class,
            "sendSmsBroadcast",
        ])->name("sms.broadcast");
        Route::post("/sms/disease-alert", [
            AdminController::class,
            "sendDiseaseAlert",
        ])->name("sms.disease-alert");

        // ── Settings tab ──────────────────────────────────────────────
        Route::get("/settings", [AdminController::class, "settings"])->name(
            "settings",
        );
        Route::post("/settings", [
            AdminController::class,
            "updateSettings",
        ])->name("settings.update");

        // ── PDF Guides (admin upload / manage) ────────────────────────
        Route::post("/guides", [GuideController::class, "store"])->name(
            "guides.store",
        );
        Route::post("/guides/{guide}/toggle", [
            GuideController::class,
            "togglePublish",
        ])->name("guides.toggle");
        Route::delete("/guides/{guide}", [
            GuideController::class,
            "destroy",
        ])->name("guides.destroy");
    });

/*
|--------------------------------------------------------------------------
| Payment Webhooks — called by mobile money providers (no auth, signed)
|--------------------------------------------------------------------------
*/

Route::post("/webhooks/payments/{provider}", [
    \App\Http\Controllers\PaymentWebhookController::class,
    "handle",
])
    ->name("webhooks.payments")
    ->withoutMiddleware([
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    ]);
