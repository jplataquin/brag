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
        Schema::table('battles', function (Blueprint $blueprint) {
            $blueprint->boolean('challenger_cancel')->default(false)->after('status');
            $blueprint->boolean('opponent_cancel')->default(false)->after('challenger_cancel');
            $blueprint->timestamp('challenger_cancel_timestamp')->nullable()->after('opponent_cancel');
            $blueprint->timestamp('opponent_cancel_timestamp')->nullable()->after('challenger_cancel_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battles', function (Blueprint $blueprint) {
            $blueprint->dropColumn([
                'challenger_cancel',
                'opponent_cancel',
                'challenger_cancel_timestamp',
                'opponent_cancel_timestamp'
            ]);
        });
    }
};
