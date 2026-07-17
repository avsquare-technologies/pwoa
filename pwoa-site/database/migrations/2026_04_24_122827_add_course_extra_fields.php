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
      Schema::table('courses', function (Blueprint $table) {
    $table->boolean('is_free')->default(true);
    $table->integer('duration_hours')->nullable();
    $table->integer('duration_minutes')->nullable();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
