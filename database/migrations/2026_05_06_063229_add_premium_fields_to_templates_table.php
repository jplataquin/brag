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
        Schema::table('templates', function (Blueprint $table) {
            $table->boolean('is_premium')->default(false)->after('game_title_id');
            $table->integer('price')->default(0)->after('is_premium');
            $table->enum('status', ['active', 'inactive'])->default('inactive')->after('price');
            $table->string('designer_name')->nullable()->after('status');
            $table->text('description')->nullable()->after('designer_name');
            $table->json('premium_config')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn(['is_premium', 'price', 'status', 'designer_name', 'description', 'premium_config']);
        });
    }
};
