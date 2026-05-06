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
        Schema::table('premium_templates', function (Blueprint $table) {
            $table->renameColumn('card_title', 'template_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('premium_templates', function (Blueprint $table) {
            $table->renameColumn('template_title', 'card_title');
        });
    }
};
