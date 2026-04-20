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
            $table->unsignedTinyInteger('level')->default(1)->after('serial_number');
            $table->string('status')->default('Maintained')->after('level'); // Maintained, Discontinued
            
            // Drop old foreign key with cascade
            $table->dropForeign(['template_id']);
            
            // Add new foreign key with restrict (or just no cascade)
            $table->foreign('template_id')
                ->references('id')
                ->on('templates')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('digital_cards', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->foreign('template_id')
                ->references('id')
                ->on('templates')
                ->onDelete('cascade');
                
            $table->dropColumn(['level', 'status']);
        });
    }
};
