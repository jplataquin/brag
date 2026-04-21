<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            $table->renameColumn('adjudicator_id', 'marshall_id');
            $table->renameColumn('adjudicator_declared_user_win', 'marshall_declared_user_win');
            $table->renameColumn('challenger_adjudicator_id', 'challenger_marshall_id');
            $table->renameColumn('opponent_adjudicator_id', 'opponent_marshall_id');
        });

        // Update battle_invites
        DB::table('battle_invites')
            ->where('role', 'adjudicator')
            ->update(['role' => 'marshall']);

        // Update battle_activities
        DB::statement("UPDATE battle_activities SET type = REPLACE(type, 'adjudicator', 'marshall')");
        DB::statement("UPDATE battle_activities SET message = REPLACE(message, 'adjudicator', 'marshall')");
        DB::statement("UPDATE battle_activities SET message = REPLACE(message, 'Adjudicator', 'Marshall')");
        DB::statement("UPDATE battle_activities SET message = REPLACE(message, 'ADJUDICATOR', 'MARSHALL')");

        // Update notifications
        DB::statement("UPDATE notifications SET type = REPLACE(type, 'adjudicator', 'marshall')");
        DB::statement("UPDATE notifications SET data = REPLACE(data, 'adjudicator', 'marshall')");
        DB::statement("UPDATE notifications SET data = REPLACE(data, 'Adjudicator', 'Marshall')");
        DB::statement("UPDATE notifications SET data = REPLACE(data, 'ADJUDICATOR', 'MARSHALL')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            $table->renameColumn('marshall_id', 'adjudicator_id');
            $table->renameColumn('marshall_declared_user_win', 'adjudicator_declared_user_win');
            $table->renameColumn('challenger_marshall_id', 'challenger_adjudicator_id');
            $table->renameColumn('opponent_marshall_id', 'opponent_adjudicator_id');
        });

        DB::table('battle_invites')
            ->where('role', 'marshall')
            ->update(['role' => 'adjudicator']);

        // Reverse activities
        DB::statement("UPDATE battle_activities SET type = REPLACE(type, 'marshall', 'adjudicator')");
        DB::statement("UPDATE battle_activities SET message = REPLACE(message, 'marshall', 'adjudicator')");
        DB::statement("UPDATE battle_activities SET message = REPLACE(message, 'Marshall', 'Adjudicator')");
        DB::statement("UPDATE battle_activities SET message = REPLACE(message, 'MARSHALL', 'ADJUDICATOR')");

        // Reverse notifications
        DB::statement("UPDATE notifications SET type = REPLACE(type, 'marshall', 'adjudicator')");
        DB::statement("UPDATE notifications SET data = REPLACE(data, 'marshall', 'adjudicator')");
        DB::statement("UPDATE notifications SET data = REPLACE(data, 'Marshall', 'Adjudicator')");
        DB::statement("UPDATE notifications SET data = REPLACE(data, 'MARSHALL', 'ADJUDICATOR')");
    }
};
