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
        Schema::table('users', function (Blueprint $table) {
            $table->string('parent_firstname')->nullable()->after('gender');
            $table->string('parent_lastname')->nullable()->after('parent_firstname');
            $table->date('parent_birthdate')->nullable()->after('parent_lastname');
            $table->string('parent_id_path')->nullable()->after('parent_birthdate');
            $table->enum('parental_consent_status', ['not_required', 'pending', 'approved'])
                ->default('not_required')
                ->after('parent_id_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'parent_firstname',
                'parent_lastname',
                'parent_birthdate',
                'parent_id_path',
                'parental_consent_status'
            ]);
        });
    }
};
