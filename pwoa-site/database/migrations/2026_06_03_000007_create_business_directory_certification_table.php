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
        if (!Schema::hasTable('business_directory_certification')) {
            Schema::create('business_directory_certification', function (Blueprint $table) {
                $table->id();
                $table->foreignId('business_id');
                $table->foreignId('directory_certification_id');
                
                $table->string('certificate_number')->nullable();
                $table->date('issued_at')->nullable();
                $table->date('expires_at')->nullable();
                $table->string('status')->default('approved');
                $table->timestamps();

                // Foreign Keys with short custom names (MySQL max 64 chars)
                $table->foreign('business_id', 'fk_bdc_biz_id')
                    ->references('id')
                    ->on('businesses')
                    ->cascadeOnDelete();

                $table->foreign('directory_certification_id', 'fk_bdc_cert_id')
                    ->references('id')
                    ->on('directory_certifications')
                    ->cascadeOnDelete();

                // Unique composite index with short custom name
                $table->unique(['business_id', 'directory_certification_id'], 'uq_bdc_biz_cert');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_directory_certification');
    }
};
