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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method')->default('hitpay')->after('payment_type');
            $table->string('proof_path')->nullable()->after('payment_method');
            $table->timestamp('auto_approve_at')->nullable()->after('collected_by');
            $table->foreignId('diamond_package_id')->nullable()->after('id')->constrained('diamond_packages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['diamond_package_id']);
            $table->dropColumn(['payment_method', 'proof_path', 'auto_approve_at', 'diamond_package_id']);
        });
    }
};
