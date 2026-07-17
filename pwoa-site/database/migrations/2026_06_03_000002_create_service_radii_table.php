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
        if (!Schema::hasTable('service_radii')) {
            Schema::create('service_radii', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedSmallInteger('value')->nullable();
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_radii');
    }
};
