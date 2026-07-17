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
            $table->foreignId('course_category_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('event_category_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_category_id');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('event_category_id');
        });
    }
};
