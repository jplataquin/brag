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
        Schema::create('premium_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_title_id')->constrained()->onDelete('cascade');
            $table->string('card_title')->unique();
            $table->integer('price')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->string('designer_name')->nullable();
            $table->text('description')->nullable();
            $table->json('premium_config');
            $table->foreignId('admin_editor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('premium_templates');
    }
};
