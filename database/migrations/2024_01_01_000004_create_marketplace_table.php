<?php
// database/migrations/2024_01_01_000004_create_marketplace_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Products ─────────────────────────────────────────────────
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('category', [
                'seeds','fertilizer','produce','livestock',
                'tools','equipment','chemicals','other'
            ]);
            $table->enum('status', ['active','inactive','sold_out','pending_review'])
                  ->default('pending_review');

            // Pricing
            $table->decimal('price', 12, 2);
            $table->string('unit')->default('unit');         // "bag","kg","head","box"
            $table->string('currency', 3)->default('MWK');
            $table->boolean('price_negotiable')->default(false);

            // Stock
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->unsignedInteger('minimum_order')->default(1);
            $table->boolean('in_stock')->default(true);

            // Location
            $table->string('province');
            $table->string('district')->nullable();

            // Media (JSON array of image paths)
            $table->json('images')->nullable();
            $table->string('thumbnail')->nullable();

            // Stats
            $table->unsignedInteger('total_views')->default(0);
            $table->unsignedInteger('total_sold')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('total_reviews')->default(0);

            // Flags
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('delivery_available')->default(true);

            $table->softDeletes();
            $table->timestamps();

            $table->index(['category', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index(['province', 'status']);
            $table->index('is_featured');
        });

        // ── Product Reviews ──────────────────────────────────────────
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('review')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        // ── Wishlists ────────────────────────────────────────────────
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        // ── Orders ───────────────────────────────────────────────────
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();        // ORD-00412

            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();

            $table->enum('status', [
                'pending','confirmed','processing',
                'dispatched','in_transit','delivered','cancelled','refunded'
            ])->default('pending');

            // Totals
            $table->decimal('subtotal',     12, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total',        12, 2);
            $table->string('currency', 3)->default('MWK');

            // Payment
            $table->enum('payment_method', [
                'airtel_money','mtn_momo','tnm_mpamba',
                'bank_transfer','cash_on_delivery'
            ])->nullable();
            $table->enum('payment_status', ['pending','paid','failed','refunded'])
                  ->default('pending');
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Delivery address
            $table->string('delivery_province')->nullable();
            $table->string('delivery_district')->nullable();
            $table->string('delivery_address')->nullable();
            $table->decimal('delivery_lat', 10, 7)->nullable();
            $table->decimal('delivery_lng', 10, 7)->nullable();

            // Timestamps for each status
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->text('cancellation_reason')->nullable();
            $table->text('buyer_notes')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index('status');
            $table->index('payment_status');
        });

        // ── Order Items ───────────────────────────────────────────────
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();

            $table->string('product_name');                  // snapshot at order time
            $table->decimal('unit_price', 12, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('total_price', 12, 2);
            $table->string('unit');

            $table->timestamps();
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('products');
    }
};
