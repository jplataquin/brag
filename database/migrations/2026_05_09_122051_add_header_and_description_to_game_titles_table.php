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
        Schema::table('game_titles', function (Blueprint $table) {
            $table->string('header_image')->nullable()->after('title');
            $table->text('description')->nullable()->after('header_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_titles', function (Blueprint $table) {
            $table->dropColumn(['header_image', 'description']);
        });
    }
};
