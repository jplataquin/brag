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
        Schema::table('team_battles', function (Blueprint $col) {
            $col->string('winner_team', 1)->nullable()->after('status');
            $col->json('team_a_card_data')->nullable()->after('winner_team');
            $col->json('team_b_card_data')->nullable()->after('team_a_card_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_battles', function (Blueprint $col) {
            $col->dropColumn(['winner_team', 'team_a_card_data', 'team_b_card_data']);
        });
    }
};
