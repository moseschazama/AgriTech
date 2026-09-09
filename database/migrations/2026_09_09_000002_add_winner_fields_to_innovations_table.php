<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('innovations', function (Blueprint $table) {
            $table->unsignedTinyInteger('winner_position')->nullable()->after('share_count');
            $table->decimal('winner_prize', 12, 2)->nullable()->after('winner_position');
            $table->timestamp('won_at')->nullable()->after('winner_prize');
        });
    }

    public function down(): void
    {
        Schema::table('innovations', function (Blueprint $table) {
            $table->dropColumn(['winner_position', 'winner_prize', 'won_at']);
        });
    }
};