<?php
// database/migrations/2024_01_01_000011_make_notification_fields_nullable.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Makes notifiable_type and notifiable_id nullable on the notifications
 * table so the system can create simple notifications without needing
 * a full Laravel polymorphic notifiable (User model) attached.
 *
 * This fixes the enrollment crash where creating the "You enrolled!"
 * notification failed because notifiable_type was NOT NULL.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table("notifications", function (Blueprint $table) {
            $table->string("notifiable_type")->nullable()->change();
            $table->unsignedBigInteger("notifiable_id")->nullable()->change();
            $table->string("action_label")->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table("notifications", function (Blueprint $table) {
            $table->string("notifiable_type")->nullable(false)->change();
            $table
                ->unsignedBigInteger("notifiable_id")
                ->nullable(false)
                ->change();
            $table->string("action_label")->nullable(false)->change();
        });
    }
};
