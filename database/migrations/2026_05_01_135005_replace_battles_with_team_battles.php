<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop the legacy 1v1 battle invites table
        Schema::dropIfExists('battle_invites');

        // 2. Drop the foreign key from battle_activities before dropping the battles table
        Schema::table('battle_activities', function (Blueprint $table) {
            if (Schema::hasColumn('battle_activities', 'battle_id')) {
                $table->dropForeign(['battle_id']);
                $table->dropColumn('battle_id');
            }
        });

        // 3. Drop the original 1v1 battles table
        Schema::dropIfExists('battles');

        // 4. Update battle_activities to rename team_battle_id to battle_id
        Schema::table('battle_activities', function (Blueprint $table) {
            if (Schema::hasColumn('battle_activities', 'team_battle_id')) {
                $table->renameColumn('team_battle_id', 'battle_id');
            }
        });

        // 5. Rename the team_battles table to battles
        Schema::rename('team_battles', 'battles');
    }

    public function down(): void
    {
        Schema::rename('battles', 'team_battles');

        Schema::table('battle_activities', function (Blueprint $table) {
            $table->renameColumn('battle_id', 'team_battle_id');
            $table->unsignedBigInteger('battle_id')->nullable()->after('id');
        });
    }
};
