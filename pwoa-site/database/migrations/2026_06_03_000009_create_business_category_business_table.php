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
        if (!Schema::hasTable('business_category_business')) {
            Schema::create('business_category_business', function (Blueprint $table) {
                $table->id();
                $table->foreignId('business_id');
                $table->foreignId('business_category_id');

                // Foreign Keys with short custom names (MySQL max 64 chars)
                $table->foreign('business_id', 'fk_bcb_biz_id')
                    ->references('id')
                    ->on('businesses')
                    ->cascadeOnDelete();

                $table->foreign('business_category_id', 'fk_bcb_cat_id')
                    ->references('id')
                    ->on('business_categories')
                    ->cascadeOnDelete();

                // Unique composite index with short custom name
                $table->unique(['business_id', 'business_category_id'], 'uq_bcb_biz_cat');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_category_business');
    }
};
