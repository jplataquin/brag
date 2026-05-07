<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            $table->string('room_slug', 10)->nullable()->after('id');
        });

        // Populate existing records with random slugs
        DB::table('battles')->get()->each(function ($battle) {
            DB::table('battles')
                ->where('id', $battle->id)
                ->update(['room_slug' => Str::random(10)]);
        });

        Schema::table('battles', function (Blueprint $table) {
            $table->string('room_slug', 10)->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            $table->dropColumn('room_slug');
        });
    }
};
