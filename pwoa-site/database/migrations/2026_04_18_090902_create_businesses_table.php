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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['contractor', 'vendor'])->default('contractor');
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->text('description')->nullable();

            // Contact & Social
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();

            // Location (Business Location)
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip', 20)->nullable();

            // Meta
            $table->string('category')->nullable()->index();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            // Performance Indexes
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
