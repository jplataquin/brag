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
        Schema::create('diamond_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('diamonds');
            $table->decimal('price', 8, 2);
            $table->decimal('promo_price', 8, 2)->nullable();
            $table->string('currency')->default('PHP');
            $table->string('qr_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_manual')->default(true);
            $table->boolean('allow_hitpay')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diamond_packages');
    }
};
