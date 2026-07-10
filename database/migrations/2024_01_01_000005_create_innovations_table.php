<?php
// database/migrations/2024_01_01_000005_create_innovations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Competitions (must exist before innovations references it) ──
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('rules')->nullable();
            $table->enum('status', ['upcoming','active','judging','closed'])
                  ->default('upcoming');
            $table->decimal('first_prize',  12, 2)->default(0);
            $table->decimal('second_prize', 12, 2)->default(0);
            $table->decimal('third_prize',  12, 2)->default(0);
            $table->decimal('entry_fee',    10, 2)->default(0);
            $table->date('starts_at');
            $table->date('ends_at');
            $table->unsignedInteger('max_entries')->nullable();
            $table->unsignedInteger('entry_count')->default(0);
            $table->string('banner_image')->nullable();
            $table->timestamps();
        });

        // ── Innovations ───────────────────────────────────────────────
        Schema::create('innovations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('category', [
                'water_management','technology','infrastructure',
                'crop_solutions','energy','post_harvest','livestock','business'
            ]);
            $table->enum('status', ['draft','pending_review','approved','rejected','featured'])
                  ->default('pending_review');

            // Impact details
            $table->string('impact_summary')->nullable();
            $table->string('estimated_cost')->nullable();
            $table->text('implementation_steps')->nullable();

            // Location
            $table->string('province')->nullable();
            $table->string('district')->nullable();

            // Media
            $table->json('images')->nullable();
            $table->string('video_url')->nullable();

            // Competition
            $table->boolean('in_competition')->default(false);
            $table->foreignId('competition_id')->nullable()
                  ->constrained('competitions')->nullOnDelete();

            // Stats
            $table->unsignedInteger('vote_count')->default(0);
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('share_count')->default(0);

            // Admin
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['category', 'status']);
            $table->index('in_competition');
            $table->index('vote_count');
        });

        // ── Innovation Votes ──────────────────────────────────────────
        Schema::create('innovation_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('innovation_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'innovation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('innovation_votes');
        Schema::dropIfExists('innovations');
        Schema::dropIfExists('competitions');
    }
};
