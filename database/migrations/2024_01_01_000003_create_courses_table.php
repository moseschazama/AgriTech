<?php
// database/migrations/2024_01_01_000003_create_courses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Instructors ─────────────────────────────────────────────
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('title')->nullable();             // "Dr.", "Prof."
            $table->string('specialization')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Courses ──────────────────────────────────────────────────
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained()->restrictOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('what_you_learn')->nullable();     // JSON array or text
            $table->enum('category', [
                'soil_crops','livestock','agri_tech',
                'agribusiness','organic','irrigation','post_harvest'
            ]);
            $table->enum('level', ['beginner','intermediate','advanced'])
                  ->default('beginner');
            $table->enum('status', ['draft','published','archived'])
                  ->default('draft');

            // Pricing
            $table->enum('access_type', ['free','paid','premium'])
                  ->default('free');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('MWK');

            // Media
            $table->string('thumbnail')->nullable();
            $table->string('preview_video')->nullable();

            // Stats (denormalized)
            $table->unsignedInteger('total_lessons')->default(0);
            $table->unsignedInteger('total_duration_minutes')->default(0);
            $table->unsignedInteger('total_enrolled')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('total_reviews')->default(0);

            // Flags
            $table->boolean('is_featured')->default(false);
            $table->boolean('has_certificate')->default(true);
            $table->boolean('works_offline')->default(false);

            $table->softDeletes();
            $table->timestamps();

            $table->index(['category', 'status']);
            $table->index(['access_type', 'status']);
            $table->index('is_featured');
        });

        // ── Lessons ──────────────────────────────────────────────────
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->enum('type', ['video','pdf','quiz','text'])->default('video');
            $table->string('video_url')->nullable();
            $table->string('pdf_path')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->boolean('is_free_preview')->default(false);
            $table->boolean('is_published')->default(true);

            $table->timestamps();
            $table->index(['course_id', 'sort_order']);
        });

        // ── Enrollments ───────────────────────────────────────────────
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            $table->enum('status', ['active','completed','suspended'])
                  ->default('active');
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->string('payment_reference')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('certificate_number')->nullable()->unique();
            $table->timestamp('certificate_issued_at')->nullable();

            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
            $table->index(['user_id', 'status']);
        });

        // ── Lesson Progress ───────────────────────────────────────────
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();

            $table->boolean('is_completed')->default(false);
            $table->unsignedInteger('watch_seconds')->default(0); // how far they watched
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();

            $table->timestamps();
            $table->unique(['user_id', 'lesson_id']);
        });

        // ── Course Reviews ────────────────────────────────────────────
        Schema::create('course_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('rating');           // 1–5
            $table->text('review')->nullable();
            $table->boolean('is_approved')->default(false);

            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
            $table->index(['course_id', 'is_approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_reviews');
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('instructors');
    }
};
