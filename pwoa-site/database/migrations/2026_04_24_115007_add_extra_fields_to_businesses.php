<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('businesses', function (Blueprint $table) {


        $table->string('tagline')->nullable();
        $table->json('service_types')->nullable();
        $table->string('membership_tier')->nullable();
        $table->boolean('is_pwoa_certified')->default(false);
        $table->float('avg_rating')->nullable();

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {

        });
    }
};
