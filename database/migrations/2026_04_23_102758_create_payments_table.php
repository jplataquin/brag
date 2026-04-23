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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reference')->unique()->comment('Our internal unique reference');
            $table->string('hitpay_id')->nullable()->comment('HitPay payment request ID');
            $table->decimal('amount', 8, 2);
            $table->string('currency')->default('PHP');
            $table->integer('shards_amount');
            $table->string('status')->default('pending')->comment('pending, completed, failed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
