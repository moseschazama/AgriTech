<?php
// database/migrations/2024_01_01_000007_create_diseases_sms_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Disease Library ───────────────────────────────────────────
        Schema::create('diseases', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('scientific_name')->nullable();
            $table->string('affected_crop');                 // "Maize"
            $table->enum('category', [
                'fungal','bacterial','viral','pest','environmental'
            ]);
            $table->enum('severity', ['Low','Medium','High','Critical'])
                  ->default('Medium');

            // Content (stored as JSON arrays for flexibility)
            $table->json('symptoms');                        // ["Yellow streaks","Stunted growth"]
            $table->text('cause');
            $table->text('spread_mechanism');
            $table->text('seasonal_info');
            $table->json('treatment_steps');                 // ["Apply pesticide early"]
            $table->json('prevention_methods');              // ["Use certified seed"]
            $table->json('recommended_products')->nullable();// [{"name":"Coragen","price":"K 185"}]
            $table->string('impact_stat')->nullable();       // "80% faster diagnosis"

            // Spread details
            $table->string('spread_vector')->nullable();     // "Leafhoppers"
            $table->string('peak_season')->nullable();       // "Nov–Feb"

            // Media
            $table->json('images')->nullable();

            // Stats
            $table->unsignedInteger('view_count')->default(0);

            $table->boolean('is_published')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['affected_crop', 'severity']);
            $table->index('category');
        });

        // ── Disease Alerts ────────────────────────────────────────────
        Schema::create('disease_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disease_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->text('description');
            $table->enum('alert_type', ['critical','warning','info'])->default('warning');
            $table->json('affected_provinces');              // ["Eastern","Central"]
            $table->json('affected_crops')->nullable();

            $table->string('recommended_action')->nullable();
            $table->string('source')->nullable();           // "Ministry of Agriculture"

            $table->boolean('sms_sent')->default(false);
            $table->timestamp('sms_sent_at')->nullable();
            $table->unsignedInteger('farmers_notified')->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();

            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['alert_type', 'is_active']);
        });

        // ── Disease Detections (AI scans by farmers) ──────────────────
        Schema::create('disease_detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('disease_id')->nullable()->constrained()->nullOnDelete();

            $table->string('image_path');
            $table->string('detected_disease')->nullable();
            $table->string('affected_crop')->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable(); // 94.00
            $table->json('all_predictions')->nullable();     // top 3 AI predictions
            $table->enum('severity', ['Low','Medium','High'])->nullable();
            $table->enum('status', ['pending','processed','failed'])->default('pending');
            $table->string('farm_location')->nullable();

            // User feedback
            $table->boolean('user_confirmed')->nullable();   // was AI correct?
            $table->text('user_notes')->nullable();

            $table->timestamps();
            $table->index(['user_id', 'status']);
        });

        // ── SMS Log ───────────────────────────────────────────────────
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sent_by')->nullable()
                  ->constrained('users')->nullOnDelete();   // null = system

            $table->enum('type', [
                'order_confirmed','order_dispatched','order_arrived',
                'payment_received','lesson_available','disease_alert',
                'weather_warning','farming_tip','custom','otp'
            ]);
            $table->text('message');
            $table->string('recipient_phone', 20);
            $table->foreignId('recipient_id')->nullable()
                  ->constrained('users')->nullOnDelete();

            // Delivery status from gateway
            $table->enum('status', ['pending','sent','delivered','failed'])
                  ->default('pending');
            $table->string('gateway_message_id')->nullable();
            $table->string('gateway', 20)->default('africas_talking'); // or 'twilio'
            $table->decimal('cost', 8, 4)->nullable();       // cost in USD

            // Related model (polymorphic-style)
            $table->string('related_type')->nullable();      // "Order","Delivery"
            $table->unsignedBigInteger('related_id')->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('recipient_phone');
            $table->index(['related_type', 'related_id']);
        });

        // ── Broadcast SMS Campaigns ────────────────────────────────────
        Schema::create('sms_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();

            $table->string('name');
            $table->text('message');
            $table->enum('type', [
                'disease_alert','lesson_alert','weather',
                'market_price','farming_tip','custom'
            ]);
            $table->json('target_provinces')->nullable();    // null = all provinces
            $table->json('target_roles')->nullable();        // null = all roles
            $table->string('target_crop')->nullable();

            $table->enum('status', ['draft','scheduled','sending','sent','failed'])
                  ->default('draft');
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedInteger('total_recipients')->default(0);
            $table->unsignedInteger('total_sent')->default(0);
            $table->unsignedInteger('total_delivered')->default(0);
            $table->unsignedInteger('total_failed')->default(0);
            $table->decimal('total_cost', 10, 4)->default(0);

            $table->timestamps();
            $table->index(['status', 'scheduled_for']);
        });

        // ── Notifications (in-app) ────────────────────────────────────
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('message');
            $table->enum('type', [
                'order','delivery','payment','lesson',
                'disease','weather','innovation','system'
            ]);
            $table->string('icon')->default('fas fa-bell');
            $table->string('icon_color')->default('var(--primary)');
            $table->string('action_url')->nullable();
            $table->string('action_label')->nullable();

            // Related model
            $table->string('notifiable_type')->nullable();
            $table->unsignedBigInteger('notifiable_id')->nullable();

            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            $table->timestamps();
            $table->index(['user_id', 'is_read']);
            $table->index(['notifiable_type', 'notifiable_id']);
        });

        // ── Weather Forecasts Cache ────────────────────────────────────
        Schema::create('weather_forecasts', function (Blueprint $table) {
            $table->id();
            $table->string('province');
            $table->string('district')->nullable();
            $table->date('forecast_date');
            $table->decimal('temp_min', 5, 1);
            $table->decimal('temp_max', 5, 1);
            $table->string('condition');                     // "Sunny","Rain","Cloudy"
            $table->unsignedTinyInteger('humidity');        // percentage
            $table->decimal('wind_speed', 5, 1)->nullable();
            $table->decimal('rainfall_mm', 6, 1)->nullable();
            $table->boolean('disease_risk')->default(false);
            $table->string('farming_advice')->nullable();
            $table->timestamp('fetched_at');
            $table->timestamps();
            $table->index(['province', 'forecast_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_forecasts');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('sms_campaigns');
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('disease_detections');
        Schema::dropIfExists('disease_alerts');
        Schema::dropIfExists('diseases');
    }
};
