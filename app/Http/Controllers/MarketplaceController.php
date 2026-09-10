<?php
// app/Http/Controllers/MarketplaceController.php

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Events\ProductCreated;
use App\Jobs\BroadcastNotification;
use App\Models\District;
use App\Models\Product;
use App\Models\Order;
use App\Models\Wishlist;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MarketplaceController extends Controller
{
    public function __construct(protected PaymentService $payments) {}

    /**
     * Main marketplace listing page — supports search, category, price
     * range, district and rating filters via query params.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Product::with([
            "seller",
            "wishlistedBy" => fn($q) => $q->where("user_id", auth()->id()),
        ])
            ->where("status", "active")
            ->where("in_stock", true);

        if ($request->q) {
            $query->where(
                fn($q) => $q
                    ->where("name", "like", "%{$request->q}%")
                    ->orWhere("description", "like", "%{$request->q}%"),
            );
        }
        if ($request->category) {
            $query->where("category", $request->category);
        }
        if ($request->district) {
            $query->where("district", $request->district);
        }
        if ($request->price_min) {
            $query->where("price", ">=", $request->price_min);
        }
        if ($request->price_max) {
            $query->where("price", "<=", $request->price_max);
        }
        if ($request->min_rating) {
            $query->where("average_rating", ">=", $request->min_rating);
        }
        if ($request->delivery) {
            $query->where("delivery_available", true);
        }

        match ($request->sort ?? "popular") {
            "newest" => $query->latest(),
            "price_low" => $query->orderBy("price"),
            "price_high" => $query->orderByDesc("price"),
            "rating" => $query->orderByDesc("average_rating"),
            default => $query->orderByDesc("total_sold"),
        };

        $products = $query->paginate(12)->withQueryString();

        $districts = District::with("tradingCentres")->orderBy("name")->get();

        return view("pages.marketplace", compact("products", "districts"));
    }

    /**
     * Single product detail page.
     */
    public function show(Product $product)
    {
        $product->load(["seller", "reviews.user"]);
        $product->recordView();

        $related = Product::active()
            ->category($product->category)
            ->where("id", "!=", $product->id)
            ->limit(4)
            ->get();

        $inWishlist = Auth::check() && Auth::user()->hasWishlisted($product);

        return view(
            "pages.product-detail",
            compact("product", "related", "inWishlist"),
        );
    }

    // ── Selling ──────────────────────────────────────────────────────

    /**
     * Store a new product listing submitted by a farmer/dealer.
     * Listings start as "pending_review" and require admin approval
     * before appearing live in the marketplace.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => ["required", "string", "max:150"],
            "category" => [
                "required",
                "in:seeds,fertilizer,produce,livestock,tools,equipment,chemicals,other",
            ],
            "description" => ["required", "string"],
            "price" => ["required", "numeric", "min:0"],
            "unit" => ["required", "string", "max:30"],
            "stock_quantity" => ["required", "integer", "min:0"],
            "minimum_order" => ["nullable", "integer", "min:1"],
            "district" => ["required", "string"],
            "delivery_available" => ["nullable", "boolean"],
            "photos.*" => ["nullable", "image", "max:4096"],
        ]);

        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile("photos")) {
            foreach ($request->file("photos") as $photo) {
                $photoPaths[] = $photo->store("products/photos", "public");
            }
        }

        $product = \App\Models\Product::create([
            ...$validated,
            "seller_id" => Auth::id(),
            "status" => "pending_review", // ← always starts pending
            "images" => $photoPaths,
            "thumbnail" => $photoPaths[0] ?? null,
            "currency" => "MWK",
            "delivery_available" => $request->boolean(
                "delivery_available",
                false,
            ),
            "minimum_order" => $validated["minimum_order"] ?? 1,
            "is_verified" => false,
        ]);

        // ── Notify the seller ──────────────────────────────────────────
        try {
            BroadcastNotification::dispatch(
                Auth::id(),
                "📋 Listing Submitted — " . $product->name,
                "Your product \"{$product->name}\" has been submitted for review.",
                "marketplace",
                "fas fa-clock",
                "#f59e0b",
                route("marketplace.my-listings"),
            );
        } catch (\Throwable $e) {
        }

        // ── Notify all admins to review ────────────────────────────────
        try {
            $admins = \App\Models\User::where("role", "admin")->get();
            foreach ($admins as $admin) {
                BroadcastNotification::dispatch(
                    $admin->id,
                    "🆕 New Product Listing for Review",
                    Auth::user()->full_name . " submitted \"{$product->name}\" for review.",
                    "marketplace",
                    "fas fa-box",
                    "var(--primary)",
                    route("admin.products"),
                );
            }
        } catch (\Throwable $e) {
        }

        // ── Broadcast product created event ────────────────────────────
        event(new ProductCreated($product));

        return back()->with(
            "success",
            "✅ \"{$product->name}\" submitted! It will go live once approved by our team (usually within 24 hours).",
        );
    }
    public function update(Request $request, Product $product)
    {
        $this->authorizeOwner($product);

        $validated = $request->validate([
            "name" => ["sometimes", "string", "max:150"],
            "price" => ["sometimes", "numeric", "min:0"],
            "stock_quantity" => ["sometimes", "integer", "min:0"],
            "description" => ["sometimes", "string", "max:2000"],
            "status" => ["sometimes", "in:active,inactive"],
        ]);

        $product->update($validated);

        return back()->with("success", "Listing updated.");
    }

    public function destroy(Product $product)
    {
        $this->authorizeOwner($product);
        $product->delete(); // soft delete

        return back()->with("success", "Listing removed.");
    }

    protected function authorizeOwner(Product $product): void
    {
        abort_unless(
            $product->seller_id === Auth::id() || Auth::user()->isAdmin(),
            403,
            "You do not own this listing.",
        );
    }

    // ── Wishlist ─────────────────────────────────────────────────────

    public function toggleWishlist(Product $product)
    {
        $added = Wishlist::toggle(Auth::id(), $product->id);

        return response()->json([
            "added" => $added,
            "message" => $added ? "Added to wishlist" : "Removed from wishlist",
        ]);
    }

    // ── Cart & Checkout ──────────────────────────────────────────────

    /**
     * Cart is stored in the session as: ['product_id' => quantity, ...]
     */
    public function addToCart(Request $request, Product $product)
    {
        $quantity = (int) $request->input("quantity", 1);

        if (!$product->hasStock($quantity)) {
            return back()->withErrors([
                "cart" => "Only {$product->stock_quantity} units of \"{$product->name}\" available.",
            ]);
        }

        $cart = session()->get("cart", []);
        $cart[$product->id] = ($cart[$product->id] ?? 0) + $quantity;
        session()->put("cart", $cart);

        return back()->with("success", "\"{$product->name}\" added to cart.");
    }

    public function updateCartItem(Request $request, Product $product)
    {
        $quantity = max(0, (int) $request->input("quantity", 1));
        $cart = session()->get("cart", []);

        if ($quantity === 0) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = $quantity;
        }

        session()->put("cart", $cart);
        return back();
    }

    public function viewCart()
    {
        $cart = session()->get("cart", []);
        $products = Product::whereIn("id", array_keys($cart))
            ->with("seller")
            ->get()
            ->keyBy("id");

        $items = collect($cart)
            ->map(
                fn($qty, $productId) => [
                    "product" => $products->get($productId),
                    "quantity" => $qty,
                    "subtotal" =>
                        ($products->get($productId)?->price ?? 0) * $qty,
                ],
            )
            ->filter(fn($item) => $item["product"] !== null);

        $total = $items->sum("subtotal");

        $districts = District::with("tradingCentres")->orderBy("name")->get();

        $defaultDistrict = old("delivery_district", Auth::user()->district);
        $deliveryFee = \App\Models\Order::calculateDeliveryFee(
            $total,
            $defaultDistrict,
        );
        $grandTotal = $total + $deliveryFee;

        return view(
            "pages.cart",
            compact(
                "items",
                "total",
                "districts",
                "deliveryFee",
                "grandTotal",
                "defaultDistrict",
            ),
        );
    }

    /**
     * Finalize checkout: build the Order from the session cart,
     * initiate the mobile money payment, then clear the cart.
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            "delivery_district" => ["required", "string"],
            "delivery_town" => ["nullable", "string", "max:200"],
            "delivery_address" => ["required", "string", "max:255"],
            "payment_method" => [
                "required",
                "in:airtel_money,tnm_mpamba,mtn_momo",
            ],
            "phone" => ["nullable", "string", "max:20"],
        ]);

        $cartItems = session("cart", []);
        if (empty($cartItems)) {
            return back()->with("error", "Your cart is empty.");
        }

        // Build order items from cart
        $items = [];
        $subtotal = 0;
        $sellerId = null;

        $cartProducts = \App\Models\Product::whereIn(
            "id",
            array_keys($cartItems),
        )
            ->get()
            ->keyBy("id");

        foreach ($cartItems as $productId => $qty) {
            $product = $cartProducts->get($productId);
            if (!$product) {
                continue;
            }

            $lineTotal = $product->price * $qty;
            $subtotal += $lineTotal;
            $sellerId = $product->seller_id;

            $items[] = [
                "product_id" => $product->id,
                "product_name" => $product->name,
                "quantity" => $qty,
                "unit_price" => $product->price,
                "total_price" => $lineTotal,
                "unit" => $product->unit ?? "unit",
            ];
        }

        if (empty($items)) {
            return back()->with("error", "No valid items in cart.");
        }

        $deliveryFee = 500; // MWK 500 flat rate — adjust as needed
        $total = $subtotal + $deliveryFee;
        $orderNumber =
            "ORD-" .
            now()->format("y") .
            "-" .
            str_pad(\App\Models\Order::count() + 1, 5, "0", STR_PAD_LEFT);

        try {
            // ── Create the Order ────────────────────────────────────────
            $order = \App\Models\Order::create([
                "order_number" => $orderNumber,
                "buyer_id" => Auth::id(),
                "seller_id" => $sellerId,
                "status" => "pending",
                "subtotal" => $subtotal,
                "delivery_fee" => $deliveryFee,
                "total" => $total,
                "currency" => "MWK",
                "payment_method" => $validated["payment_method"],
                "payment_status" => "pending",
                "delivery_district" => $validated["delivery_district"],
                "delivery_town" => $validated["delivery_town"] ?? null,
                "delivery_address" => $validated["delivery_address"],
            ]);

            // Auto-resolve coordinates from district/town names
            $order->resolveDeliveryCoordinates();
            $order->refresh();

            // ── Create Order Items ──────────────────────────────────────
            foreach ($items as $item) {
                $order->items()->create($item);

                // Reduce stock for each product
                try {
                    \App\Models\Product::where(
                        "id",
                        $item["product_id"],
                    )->decrement("stock_quantity", $item["quantity"]);
                } catch (\Throwable $e) {
                }
            }

            // ── Create Delivery Record (fixes "No active deliveries") ───
            $tracking =
                "TRK-" .
                now()->format("Ymd") .
                "-" .
                strtoupper(\Illuminate\Support\Str::random(6));

            try {
                $originProduct = \App\Models\Product::find(array_key_first($cartItems));
                $originDistrict = $originProduct?->district ?? "Lilongwe";
                $originCoords = \App\Models\Order::resolveOriginCoordinates($originDistrict);

                \App\Models\Delivery::create([
                    "order_id" => $order->id,
                    "tracking_number" => $tracking,
                    "status" => "pending",
                    "origin_address" => \App\Models\Order::originAddressFor($originProduct?->seller, $originDistrict),
                    "origin_district" => $originDistrict,
                    "origin_lat" => $originCoords['origin_lat'] ?? null,
                    "origin_lng" => $originCoords['origin_lng'] ?? null,
                    "destination_district" => $validated["delivery_district"],
                    "destination_address" => $validated["delivery_address"],
                    "destination_lat" => $order->delivery_lat,
                    "destination_lng" => $order->delivery_lng,
                ]);
            } catch (\Throwable $e) {
                // Delivery table issue — don't block the order
            }

            // ── Notify Buyer ────────────────────────────────────────────
            try {
                BroadcastNotification::dispatch(
                    Auth::id(),
                    "✅ Order Placed — " . $orderNumber,
                    "Your order has been received! Total: MWK " . number_format($total) . ". Please complete " . ucwords(str_replace("_", " ", $validated["payment_method"])) . " payment of MWK " . number_format($total) . " to confirm.",
                    "order",
                    "fas fa-shopping-bag",
                    "var(--primary)",
                    route("marketplace.my-orders"),
                );
            } catch (\Throwable $e) {
            }

            // ── Notify Seller ───────────────────────────────────────────
            if ($sellerId) {
                try {
                    BroadcastNotification::dispatch(
                        $sellerId,
                        "🛒 New Order Received — " . $orderNumber,
                        "New order for MWK " . number_format($total) . " from " . Auth::user()->full_name,
                        "order",
                        "fas fa-box",
                        "var(--earth-500)",
                        route("marketplace.my-listings"),
                    );
                } catch (\Throwable $e) {
                }
            }

            // ── Notify All Admins ──────────────────────────────────────
            try {
                $admins = \App\Models\User::where("role", "admin")->get();
                foreach ($admins as $admin) {
                    BroadcastNotification::dispatch(
                        $admin->id,
                        "📋 New Order — " . $orderNumber,
                        "Order #" . $orderNumber . " for MWK " . number_format($total) . " placed by " . Auth::user()->full_name . ".",
                        "order",
                        "fas fa-clipboard-check",
                        "var(--primary)",
                        route("admin.orders"),
                    );
                }
            } catch (\Throwable $e) {
            }

            // ── Broadcast OrderPlaced event ────────────────────────────
            event(new OrderPlaced($order));

            // ── Clear Cart ──────────────────────────────────────────────
            session()->forget("cart");

            return redirect()
                ->route("marketplace.order.confirmation", $order)
                ->with("success", "Order {$orderNumber} placed successfully!");
        } catch (\Throwable $e) {
            return back()->with(
                "error",
                "Could not place order: " . $e->getMessage(),
            );
        }
    }

    public function orderConfirmation(\App\Models\Order $order)
    {
        abort_unless($order->buyer_id === Auth::id(), 403);
        $districts = District::with("tradingCentres")->orderBy("name")->get();
        $order->load(["items", "seller", "buyer", "delivery"]);
        return view("pages.order-confirmation", compact("order", "districts"));
    }

    public function myOrders()
    {
        $orders = \App\Models\Order::with(["items", "delivery"])
            ->where("buyer_id", Auth::id())
            ->latest()
            ->paginate(10);

        $districts = District::with("tradingCentres")->orderBy("name")->get();
        return view("pages.my-orders", compact("orders", "districts"));
    }

    public function reportIssue(Request $request, \App\Models\Order $order)
    {
        abort_unless($order->buyer_id === Auth::id(), 403);

        $validated = $request->validate([
            "subject" => ["required", "string", "max:200"],
            "message" => ["required", "string", "max:1000"],
        ]);

        try {
            $admins = \App\Models\User::where("role", "admin")->get();
            foreach ($admins as $admin) {
                BroadcastNotification::dispatch(
                    $admin->id,
                    "⚠️ Issue Reported — Order #" . $order->order_number,
                    Auth::user()->full_name . " reported: \"{$validated['subject']}\"",
                    "order",
                    "fas fa-exclamation-triangle",
                    "#ef4444",
                    route("admin.orders"),
                );
            }

            return back()->with(
                "success",
                "Your issue has been reported. An admin will contact you shortly.",
            );
        } catch (\Throwable $e) {
            return back()->with(
                "error",
                "Failed to report issue. Please try again or call support.",
            );
        }
    }

    public function myListings()
    {
        $products = \App\Models\Product::where("seller_id", Auth::id())
            ->withCount("orderItems")
            ->latest()
            ->paginate(10);

        $districts = District::with("tradingCentres")->orderBy("name")->get();
        return view("pages.my-listings", compact("products", "districts"));
    }
}
