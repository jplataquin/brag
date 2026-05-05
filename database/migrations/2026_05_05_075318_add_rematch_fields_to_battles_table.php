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
        Schema::table('battles', function (Blueprint $table) {
            $table->foreignId('team_a_rematch_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('team_b_rematch_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rematch_battle_id')->nullable()->constrained('battles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            $table->dropForeign(['team_a_rematch_user_id']);
            $table->dropForeign(['team_b_rematch_user_id']);
            $table->dropForeign(['rematch_battle_id']);
            $table->dropColumn(['team_a_rematch_user_id', 'team_b_rematch_user_id', 'rematch_battle_id']);
        });
    }
};
