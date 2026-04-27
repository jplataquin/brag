<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('battle_activities', function (Blueprint $table) {
            $table->foreignId('team_battle_id')->nullable()->after('battle_id')->constrained('team_battles')->onDelete('cascade');
            $table->foreignId('battle_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battle_activities', function (Blueprint $table) {
            $table->dropForeign(['team_battle_id']);
            $table->dropColumn('team_battle_id');
            $table->foreignId('battle_id')->nullable(false)->change();
        });
    }
};
