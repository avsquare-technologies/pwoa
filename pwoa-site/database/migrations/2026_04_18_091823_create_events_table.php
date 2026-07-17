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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->integer('capacity')->nullable();

            // Pricing
            $table->decimal('price', 10, 2)->default(0.00); // 0 = Free for all
            $table->boolean('is_free_for_members')->default(true);

            $table->string('image_path')->nullable();
            $table->enum('status', ['draft', 'published', 'cancelled'])->default('draft');

            $table->timestamps();

            $table->index('status');
            $table->index('starts_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
