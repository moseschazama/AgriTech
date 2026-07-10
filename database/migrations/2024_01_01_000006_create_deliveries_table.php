<?php
// database/migrations/2024_01_01_000006_create_deliveries_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Drivers ───────────────────────────────────────────────────
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('license_number')->unique();
            $table->string('vehicle_type');                  // "Toyota Hilux"
            $table->string('vehicle_plate')->unique();       // "ABY 4521"
            $table->enum('status', ['available','on_delivery','offline','suspended'])
                  ->default('available');

            // Real-time location (updated every 30 seconds during delivery)
            $table->decimal('current_lat', 10, 7)->nullable();
            $table->decimal('current_lng', 10, 7)->nullable();
            $table->timestamp('location_updated_at')->nullable();

            // Stats
            $table->unsignedInteger('total_deliveries')->default(0);
            $table->decimal('average_rating', 3, 2)->default(5.0);
            $table->unsignedInteger('total_reviews')->default(0);

            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->index('status');
        });

        // ── Deliveries ────────────────────────────────────────────────
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();     // TRK-20240601-001

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('status', [
                'pending','assigned','collected',
                'in_transit','near_destination','delivered','failed'
            ])->default('pending');

            // Origin (seller)
            $table->string('origin_address');
            $table->string('origin_province');
            $table->decimal('origin_lat', 10, 7)->nullable();
            $table->decimal('origin_lng', 10, 7)->nullable();

            // Destination (buyer)
            $table->string('destination_address');
            $table->string('destination_province');
            $table->decimal('destination_lat', 10, 7)->nullable();
            $table->decimal('destination_lng', 10, 7)->nullable();

            // Driver current position (updated live)
            $table->decimal('driver_current_lat', 10, 7)->nullable();
            $table->decimal('driver_current_lng', 10, 7)->nullable();
            $table->unsignedInteger('distance_remaining_km')->nullable();
            $table->timestamp('driver_location_updated_at')->nullable();

            // Timing
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('estimated_arrival_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // Delivery notes
            $table->text('delivery_notes')->nullable();
            $table->string('proof_of_delivery')->nullable(); // photo path
            $table->text('failure_reason')->nullable();
            $table->unsignedTinyInteger('buyer_rating')->nullable(); // 1-5

            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['driver_id', 'status']);
            $table->index('tracking_number');
        });

        // ── Delivery Status Log ───────────────────────────────────────
        Schema::create('delivery_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained()->cascadeOnDelete();

            $table->string('status');
            $table->string('location')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('updated_by')->nullable()
                  ->constrained('users')->nullOnDelete();

            $table->timestamp('created_at');
            $table->index(['delivery_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_status_logs');
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('drivers');
    }
};
