<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL ENUM cannot be altered via ->change(), use raw statement
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'pending','confirmed','packing','processing',
            'dispatched','on_the_way','in_transit',
            'delivered','cancelled','refunded'
        ) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'pending','confirmed','processing','dispatched',
            'in_transit','delivered','cancelled','refunded'
        ) NOT NULL DEFAULT 'pending'");
    }
};
