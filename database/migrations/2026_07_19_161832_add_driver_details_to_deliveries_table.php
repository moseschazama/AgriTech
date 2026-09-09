<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('driver_name')->nullable()->after('driver_id');
            $table->string('driver_phone')->nullable()->after('driver_name');
            $table->string('driver_vehicle_plate')->nullable()->after('driver_phone');
            $table->string('driver_vehicle_type')->nullable()->after('driver_vehicle_plate');
            $table->unsignedBigInteger('driver_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['driver_name', 'driver_phone', 'driver_vehicle_plate', 'driver_vehicle_type']);
        });
    }
};
