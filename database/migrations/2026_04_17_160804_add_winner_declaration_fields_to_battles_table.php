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
            $table->foreignId('challenger_declared_user_win')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('opponent_declared_user_win')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('adjudicator_declared_user_win')->nullable()->constrained('users')->onDelete('set null');
        });

        // Add 'failed' to enum
        DB::statement("ALTER TABLE battles MODIFY COLUMN status ENUM('pending', 'ready', 'active', 'completed', 'cancelled', 'failed') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            $table->dropForeign(['challenger_declared_user_win']);
            $table->dropForeign(['opponent_declared_user_win']);
            $table->dropForeign(['adjudicator_declared_user_win']);
            $table->dropColumn([
                'challenger_declared_user_win',
                'opponent_declared_user_win',
                'adjudicator_declared_user_win'
            ]);
        });

        DB::statement("ALTER TABLE battles MODIFY COLUMN status ENUM('pending', 'ready', 'active', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};
