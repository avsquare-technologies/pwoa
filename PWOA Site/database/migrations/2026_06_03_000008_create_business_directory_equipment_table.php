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
        if (!Schema::hasTable('business_directory_equipment')) {
            Schema::create('business_directory_equipment', function (Blueprint $table) {
                $table->id();
                $table->foreignId('business_id');
                $table->foreignId('directory_equipment_id');
                
                $table->unsignedSmallInteger('quantity')->default(1);
                $table->text('specifications')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->string('verification_photo_path')->nullable();
                $table->timestamps();

                // Foreign Keys with short custom names (MySQL max 64 chars)
                $table->foreign('business_id', 'fk_bde_biz_id')
                    ->references('id')
                    ->on('businesses')
                    ->cascadeOnDelete();

                $table->foreign('directory_equipment_id', 'fk_bde_equip_id')
                    ->references('id')
                    ->on('directory_equipments')
                    ->cascadeOnDelete();

                // Unique composite index with short custom name
                $table->unique(['business_id', 'directory_equipment_id'], 'uq_bde_biz_equip');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_directory_equipment');
    }
};
