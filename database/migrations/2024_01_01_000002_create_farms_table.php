<?php
// database/migrations/2024_01_01_000002_create_farms_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('name');                          // "Mutale Family Farm"
            $table->decimal('size_hectares', 8, 2)->nullable();
            $table->enum('farm_type', [
                'small_scale','commercial','livestock',
                'mixed','organic','agribusiness'
            ])->default('small_scale');

            // Location
            $table->string('province');
            $table->string('district')->nullable();
            $table->string('village')->nullable();
            $table->decimal('latitude',  10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Farming details
            $table->json('primary_crops')->nullable();       // ["maize","soybeans"]
            $table->json('livestock')->nullable();           // {"cattle":12,"chickens":80}
            $table->enum('irrigation_type', [
                'rain_fed','drip','sprinkler','flood','borehole'
            ])->default('rain_fed');
            $table->boolean('has_storage')->default(false);
            $table->boolean('has_greenhouse')->default(false);
            $table->boolean('is_organic_certified')->default(false);

            // Verification
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()
                  ->constrained('users')->nullOnDelete();

            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['province', 'farm_type']);
            $table->index('is_verified');
        });

        // Farm production records
        Schema::create('farm_productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();

            $table->string('season');                        // "2025/26"
            $table->string('crop');
            $table->decimal('yield_per_hectare', 8, 2)->nullable();
            $table->decimal('total_yield', 10, 2)->nullable();
            $table->decimal('revenue', 12, 2)->nullable();
            $table->string('currency', 3)->default('MWK');
            $table->date('planted_at')->nullable();
            $table->date('harvested_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->index(['farm_id', 'season']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_productions');
        Schema::dropIfExists('farms');
    }
};
