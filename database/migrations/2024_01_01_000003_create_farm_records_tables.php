<?php
// database/migrations/2024_01_01_000003_create_farm_records_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Farm Seasons ──────────────────────────────────────────────
        Schema::create('farm_seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            
            $table->string('name');                                    // "2025/26 Main Season"
            $table->string('crop');
            $table->enum('status', ['planning','active','harvested','completed'])->default('planning');
            
            $table->decimal('field_size_hectares', 8, 2)->nullable();
            $table->string('field_name')->nullable();                  // "North Field", "Plot A"
            
            // Dates
            $table->date('planted_at')->nullable();
            $table->date('expected_harvest_at')->nullable();
            $table->date('harvested_at')->nullable();
            
            // Yields
            $table->decimal('expected_yield_kg', 12, 2)->nullable();
            $table->decimal('actual_yield_kg', 12, 2)->nullable();
            $table->decimal('yield_per_hectare', 8, 2)->nullable();
            
            // Revenue
            $table->decimal('expected_revenue', 14, 2)->nullable();
            $table->decimal('actual_revenue', 14, 2)->nullable();
            $table->string('currency', 3)->default('MWK');
            
            // Costs summary (computed from farm_costs)
            $table->decimal('total_costs', 14, 2)->default(0);
            $table->decimal('net_profit', 14, 2)->default(0);
            $table->decimal('roi_percentage', 6, 2)->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['farm_id', 'status']);
            $table->index(['farm_id', 'crop']);
            $table->index(['farm_id', 'created_at']);
        });

        // ── Farm Costs (inputs) ──────────────────────────────────────
        Schema::create('farm_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('farm_seasons')->cascadeOnDelete();
            
            $table->enum('category', [
                'seed','fertilizer','chemicals','labour','transport',
                'irrigation','equipment','rent','other'
            ]);
            $table->string('item_name');                               // "NPK Fertilizer 50kg"
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('unit')->nullable();                        // "kg", "bags", "hours"
            $table->decimal('unit_cost', 12, 2);
            $table->decimal('total_cost', 14, 2);
            $table->string('currency', 3)->default('MWK');
            
            $table->date('purchased_at')->nullable();
            $table->string('supplier')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['season_id', 'category']);
        });

        // ── Farm Sales (revenue) ─────────────────────────────────────
        Schema::create('farm_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('farm_seasons')->cascadeOnDelete();
            
            $table->string('crop');
            $table->decimal('quantity_kg', 12, 2);
            $table->decimal('price_per_kg', 10, 2);
            $table->decimal('total_revenue', 14, 2);
            $table->string('currency', 3)->default('MWK');
            
            $table->string('buyer_name')->nullable();
            $table->string('buyer_type')->nullable();                  // "market", "processor", "individual"
            $table->string('market')->nullable();                      // "Lilongwe Market", "Online"
            $table->date('sold_at');
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['season_id', 'sold_at']);
        });

        // ── Farm Events (activities, weather, disease) ────────────────
        Schema::create('farm_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('farm_seasons')->cascadeOnDelete();
            
            $table->enum('type', [
                'activity','weather','disease','pest','treatment',
                'observation','milestone','alert'
            ]);
            $table->string('title');
            $table->text('description')->nullable();
            
            $table->date('occurred_at');
            $table->string('severity')->nullable();                    // "low","medium","high","critical"
            
            // For weather events
            $table->decimal('temperature_c', 5, 1)->nullable();
            $table->decimal('rainfall_mm', 6, 1)->nullable();
            $table->string('weather_condition')->nullable();
            
            // For treatment events
            $table->string('product_used')->nullable();
            $table->decimal('dosage', 10, 2)->nullable();
            $table->string('dosage_unit')->nullable();
            
            $table->boolean('requires_follow_up')->default(false);
            $table->date('follow_up_date')->nullable();
            $table->boolean('is_resolved')->default(false);
            
            $table->timestamps();
            
            $table->index(['season_id', 'type']);
            $table->index(['season_id', 'occurred_at']);
        });

        // ── Admin Alerts ─────────────────────────────────────────────
        Schema::create('farm_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('season_id')->nullable()->constrained('farm_seasons')->nullOnDelete();
            
            $table->enum('type', [
                'cost_anomaly','low_yield','missed_planting','weather_risk',
                'disease_outbreak','deadline','recommendation','admin_intervention'
            ]);
            $table->string('title');
            $table->text('message');
            $table->enum('priority', ['low','medium','high','urgent'])->default('medium');
            
            $table->boolean('is_read')->default(false);
            $table->boolean('is_dismissed')->default(false);
            $table->boolean('requires_action')->default(false);
            $table->string('action_url')->nullable();
            
            $table->timestamp('dismissed_at')->nullable();
            $table->foreignId('dismissed_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            
            $table->index(['farm_id', 'is_read']);
            $table->index(['farm_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_alerts');
        Schema::dropIfExists('farm_events');
        Schema::dropIfExists('farm_sales');
        Schema::dropIfExists('farm_costs');
        Schema::dropIfExists('farm_seasons');
    }
};
