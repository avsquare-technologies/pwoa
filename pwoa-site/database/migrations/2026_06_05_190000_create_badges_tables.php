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
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description', 500)->nullable();
            $table->string('icon_path', 500)->nullable();
            $table->string('color', 20)->default('primary');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('badge_business', function (Blueprint $table) {
            $table->id();
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['badge_id', 'business_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badge_business');
        Schema::dropIfExists('badges');
    }
};
