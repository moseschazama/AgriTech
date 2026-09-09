<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('packed_at')->nullable()->after('confirmed_at');
            $table->timestamp('on_the_way_at')->nullable()->after('dispatched_at');
        });

        Schema::create('tracking_stickers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_id')->constrained()->cascadeOnDelete();
            $table->string('sticker_code', 30)->unique();
            $table->text('qr_data')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('scanned_at')->nullable();
            $table->string('scan_location')->nullable();
            $table->decimal('scan_lat', 10, 7)->nullable();
            $table->decimal('scan_lng', 10, 7)->nullable();
            $table->timestamps();
            $table->index('sticker_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_stickers');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['packed_at', 'on_the_way_at']);
        });
    }
};
