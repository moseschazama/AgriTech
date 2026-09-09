<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('users')->whereNull('email')->update([
            'email' => DB::raw("CONCAT('user_', id, '@deleted.agritech')"),
        ]);
        Schema::table('users', function (Blueprint $table) {
            $table->string('email', 255)->nullable(false)->change();
        });
    }
};
