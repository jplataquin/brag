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
            $blueprint->foreignId('challenger_adjudicator_id')->nullable()->constrained('users')->nullOnDelete();
            $blueprint->foreignId('opponent_adjudicator_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battles', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['challenger_adjudicator_id']);
            $blueprint->dropColumn('challenger_adjudicator_id');
            $blueprint->dropForeign(['opponent_adjudicator_id']);
            $blueprint->dropColumn('opponent_adjudicator_id');
        });
    }
};
