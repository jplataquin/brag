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
            $table->renameColumn('arbiter_id', 'adjudicator_id');
        });

        // Update existing data in battle_invites
        DB::table('battle_invites')->where('role', 'arbiter')->update(['role' => 'adjudicator']);

        // Modify the enum column
        // Note: This is MySQL specific as per GEMINI.md project requirements
        DB::statement("ALTER TABLE battle_invites MODIFY COLUMN role ENUM('opponent', 'adjudicator') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            $table->renameColumn('adjudicator_id', 'arbiter_id');
        });

        DB::table('battle_invites')->where('role', 'adjudicator')->update(['role' => 'arbiter']);

        DB::statement("ALTER TABLE battle_invites MODIFY COLUMN role ENUM('opponent', 'arbiter') NOT NULL");
    }
};
