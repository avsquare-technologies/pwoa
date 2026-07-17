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
        if (!Schema::hasTable('contractor_details')) {
            Schema::create('contractor_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('business_id')->unique()->constrained()->cascadeOnDelete();
                $table->unsignedTinyInteger('years_in_business')->nullable();
                $table->string('license_number')->nullable();
                $table->boolean('is_insured')->default(false);
                $table->foreignId('service_radius_id')->nullable()->constrained('service_radii')->nullOnDelete();
                $table->boolean('is_emergency_service')->default(false);
                $table->boolean('is_subcontracting')->default(false);
                $table->boolean('is_national_accounts')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_details');
    }
};
