<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * NOTE: This migration has been nullified because the base migrations
     * (2024_01_02_000003_create_battles_table.php and 2024_01_02_000004_create_battle_invites_table.php)
     * were manually updated to use 'adjudicator' instead of 'arbiter'.
     * This file remains to prevent "migration not found" errors in existing environments.
     */
    public function up(): void
    {
        // No-op: Columns already named correctly in base migrations.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
