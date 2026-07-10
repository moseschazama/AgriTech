<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── District → Region mapping ──
        $regionMap = $this->getRegionMap();

        // ── users: already has district, migrate province → district, add region + trading_centre ──
        Schema::table('users', function (Blueprint $table) {
            $table->string('region')->nullable()->after('district');
            $table->string('trading_centre')->nullable()->after('region');
        });
        DB::table('users')->whereNull('district')->whereNotNull('province')->get()->each(function ($user) use ($regionMap) {
            DB::table('users')->where('id', $user->id)->update([
                'district' => $user->province,
                'region'   => $regionMap[$user->province] ?? null,
            ]);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['province']);
            $table->dropColumn('province');
        });

        // ── farms: already has district, migrate province → district ──
        DB::statement('UPDATE farms SET district = province WHERE (district IS NULL OR district = "") AND province IS NOT NULL');
        Schema::table('farms', function (Blueprint $table) {
            $table->string('region')->nullable()->after('district');
            $table->dropIndex(['province', 'farm_type']);
            $table->dropColumn('province');
            $table->index(['district', 'farm_type']);
        });
        // Populate region
        DB::table('farms')->whereNotNull('district')->get()->each(function ($farm) use ($regionMap) {
            DB::table('farms')->where('id', $farm->id)->update(['region' => $regionMap[$farm->district] ?? null]);
        });

        // ── products: already has district, migrate province → district ──
        DB::statement('UPDATE products SET district = province WHERE (district IS NULL OR district = "") AND province IS NOT NULL');
        Schema::table('products', function (Blueprint $table) {
            $table->string('region')->nullable()->after('district');
            $table->dropIndex(['province', 'status']);
            $table->dropColumn('province');
            $table->index(['district', 'status']);
        });
        DB::table('products')->whereNotNull('district')->get()->each(function ($p) use ($regionMap) {
            DB::table('products')->where('id', $p->id)->update(['region' => $regionMap[$p->district] ?? null]);
        });

        // ── orders: already has delivery_district, migrate delivery_province → delivery_district ──
        DB::statement('UPDATE orders SET delivery_district = delivery_province WHERE (delivery_district IS NULL OR delivery_district = "") AND delivery_province IS NOT NULL');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_province');
        });

        // ── innovations: already has district, migrate province → district ──
        DB::statement('UPDATE innovations SET district = province WHERE (district IS NULL OR district = "") AND province IS NOT NULL');
        Schema::table('innovations', function (Blueprint $table) {
            $table->dropColumn('province');
        });

        // ── weather_forecasts: already has district, migrate province → district ──
        DB::statement('UPDATE weather_forecasts SET district = province WHERE (district IS NULL OR district = "") AND province IS NOT NULL');
        Schema::table('weather_forecasts', function (Blueprint $table) {
            $table->dropIndex(['province', 'forecast_date']);
            $table->dropColumn('province');
            $table->index(['district', 'forecast_date']);
        });

        // ── deliveries: NO district columns — add them ──
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('origin_district')->nullable()->after('origin_province');
            $table->string('origin_region')->nullable()->after('origin_district');
            $table->string('destination_district')->nullable()->after('destination_province');
            $table->string('destination_region')->nullable()->after('destination_district');
        });
        DB::table('deliveries')->whereNotNull('origin_province')->get()->each(function ($d) use ($regionMap) {
            DB::table('deliveries')->where('id', $d->id)->update([
                'origin_district'      => $d->origin_province,
                'origin_region'        => $regionMap[$d->origin_province] ?? null,
                'destination_district' => $d->destination_province,
                'destination_region'   => $regionMap[$d->destination_province] ?? null,
            ]);
        });
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['origin_province', 'destination_province']);
        });

        // ── disease_alerts: JSON — add affected_districts ──
        Schema::table('disease_alerts', function (Blueprint $table) {
            $table->json('affected_districts')->nullable()->after('affected_provinces');
        });
        DB::table('disease_alerts')->whereNotNull('affected_provinces')->get()->each(function ($a) {
            DB::table('disease_alerts')->where('id', $a->id)->update(['affected_districts' => $a->affected_provinces]);
        });
        Schema::table('disease_alerts', function (Blueprint $table) {
            $table->dropColumn('affected_provinces');
        });

        // ── sms_campaigns: JSON — add target_districts ──
        Schema::table('sms_campaigns', function (Blueprint $table) {
            $table->json('target_districts')->nullable()->after('target_provinces');
        });
        DB::table('sms_campaigns')->whereNotNull('target_provinces')->get()->each(function ($c) {
            DB::table('sms_campaigns')->where('id', $c->id)->update(['target_districts' => $c->target_provinces]);
        });
        Schema::table('sms_campaigns', function (Blueprint $table) {
            $table->dropColumn('target_provinces');
        });
    }

    public function down(): void
    {
        throw new \Exception('This migration cannot be safely reversed.');
    }

    private function getRegionMap(): array
    {
        $districtRegions = [
            'Northern' => ['Chitipa','Karonga','Likoma','Mzimba','Nkhata Bay','Rumphi'],
            'Central'  => ['Dedza','Dowa','Kasungu','Lilongwe','Mchinji','Mangochi','Nkhotakota','Ntchisi','Salima'],
            'Southern' => ['Balaka','Blantyre','Chikwawa','Chiradzulu','Machinga','Mulanje','Mwanza','Neno','Ntcheu','Phalombe','Thyolo','Zomba'],
        ];
        $map = [];
        foreach ($districtRegions as $region => $districts) {
            foreach ($districts as $d) {
                $map[$d] = $region;
            }
        }
        return $map;
    }
};
