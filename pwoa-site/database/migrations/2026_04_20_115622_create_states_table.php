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
        Schema::create('states', function (Blueprint $table) {
            $table->unsignedMediumInteger('id')->primary();
            $table->string('name', 255);
            $table->unsignedMediumInteger('country_id');
            $table->char('country_code', 2);
            $table->string('fips_code', 255)->nullable();
            $table->string('iso2', 255)->nullable();
            $table->string('type', 191)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('flag')->default(true);
            $table->string('wikiDataId', 255)->nullable();
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
