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
        // Add 'ready' to enum
        DB::statement("ALTER TABLE battles MODIFY COLUMN status ENUM('pending', 'ready', 'active', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First convert 'ready' back to 'pending' if any exists
        DB::table('battles')->where('status', 'ready')->update(['status' => 'pending']);
        
        DB::statement("ALTER TABLE battles MODIFY COLUMN status ENUM('pending', 'active', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};
