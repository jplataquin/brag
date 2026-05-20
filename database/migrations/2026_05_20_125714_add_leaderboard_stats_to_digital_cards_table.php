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
        Schema::table('digital_cards', function (Blueprint $table) {
            $table->decimal('win_rate', 5, 2)->default(0)->after('losses');
            $table->decimal('integrity_stat', 5, 2)->default(0)->after('win_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('digital_cards', function (Blueprint $table) {
            $table->dropColumn(['win_rate', 'integrity_stat']);
        });
    }
};
