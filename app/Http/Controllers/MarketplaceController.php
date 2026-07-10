<?php
// app/Http/Controllers/MarketplaceController.php

namespace App\Http\Controllers;

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
        $query = \App\Models\Product::with("seller")
            ->where("status", "active") // ← only admin-approved listings
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
                "in:seeds,fertilizer,fresh_produce,livestock,tools,equipment,chemicals,other",
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
            "photos" => $photoPaths,
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
            \App\Models\Notification::create([
                "user_id" => Auth::id(),
                "title" => "📋 Listing Submitted — " . $product->name,
                "message" =>
                    "Your product \"{$product->name}\" has been submitted and is now under review. " .
                    "You'll receive a notification once it's approved and live to buyers. " .
                    "This usually takes less than 24 hours.",
                "type" => "marketplace",
                "icon" => "fas fa-clock",
                "icon_color" => "#f59e0b",
                "action_url" => route("marketplace.my-listings"),
                "is_read" => false,
            ]);
        } catch (\Throwable $e) {
        }

        // ── Notify all admins to review ────────────────────────────────
        try {
            $admins = \App\Models\User::where("role", "admin")->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    "user_id" => $admin->id,
                    "title" => "🆕 New Product Listing for Review",
                    "message" =>
                        Auth::user()->full_name .
                        " has submitted a new product listing: \"{$product->name}\" " .
                        "priced at MWK " .
                        number_format($product->price) .
                        "/{$product->unit}. " .
                        "Please review and approve or reject it in the Admin Panel.",
                    "type" => "marketplace",
                    "icon" => "fas fa-box",
                    "icon_color" => "var(--primary)",
                    "action_url" => route("admin.products"),
                    "is_read" => false,
                ]);
            }
        } catch (\Throwable $e) {
        }

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

        return view("pages.cart", compact("items", "total", "districts"));
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
                "in:airtel_money,tnm_mpamba,mtn_momo,cash_on_delivery",
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

        foreach ($cartItems as $productId => $qty) {
            $product = \App\Models\Product::find($productId);
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
                \App\Models\Delivery::create([
                    "order_id" => $order->id,
                    "tracking_number" => $tracking,
                    "status" => "pending",
                    "origin_district" =>
                        \App\Models\Product::find(array_key_first($cartItems))
                            ?->district ?? "Lilongwe",
                    "destination_district" => $validated["delivery_district"],
                    "destination_address" => $validated["delivery_address"],
                ]);
            } catch (\Throwable $e) {
                // Delivery table issue — don't block the order
            }

            // ── Notify Buyer ────────────────────────────────────────────
            try {
                \App\Models\Notification::create([
                    "user_id" => Auth::id(),
                    "title" => "✅ Order Placed — " . $orderNumber,
                    "message" =>
                        "Your order has been received! Total: MWK " .
                        number_format($total) .
                        ". " .
                        ($validated["payment_method"] !== "cash_on_delivery"
                            ? "Please complete " .
                                ucwords(
                                    str_replace(
                                        "_",
                                        " ",
                                        $validated["payment_method"],
                                    ),
                                ) .
                                " payment of MWK " .
                                number_format($total) .
                                " to confirm."
                            : "Pay MWK " .
                                number_format($total) .
                                " to the driver on delivery."),
                    "type" => "order",
                    "icon" => "fas fa-shopping-bag",
                    "icon_color" => "var(--primary)",
                    "action_url" => route("marketplace.my-orders"),
                    "is_read" => false,
                ]);
            } catch (\Throwable $e) {
            }

            // ── Notify Seller ───────────────────────────────────────────
            if ($sellerId) {
                try {
                    \App\Models\Notification::create([
                        "user_id" => $sellerId,
                        "title" => "🛒 New Order Received — " . $orderNumber,
                        "message" =>
                            "You have a new order for MWK " .
                            number_format($total) .
                            " from " .
                            Auth::user()->full_name .
                            ". Please prepare the items for dispatch.",
                        "type" => "order",
                        "icon" => "fas fa-box",
                        "icon_color" => "var(--earth-500)",
                        "action_url" => route("marketplace.my-listings"),
                        "is_read" => false,
                    ]);
                } catch (\Throwable $e) {
                }
            }

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
