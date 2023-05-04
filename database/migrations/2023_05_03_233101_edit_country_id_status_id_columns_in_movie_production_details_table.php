<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('movie_production_details', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->change();
            $table->unsignedBigInteger('status_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movie_production_details', function (Blueprint $table) {
            //
        });
    }
};
