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
        if (!Schema::hasTable('vendor_details')) {
            Schema::create('vendor_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('business_id')->unique()->constrained()->cascadeOnDelete();
                $table->unsignedTinyInteger('years_in_business')->nullable();
                $table->boolean('has_online_ordering')->default(false);
                $table->boolean('has_local_pickup')->default(false);
                $table->boolean('has_member_discounts')->default(false);
                $table->boolean('wants_preferred_program')->default(false);
                $table->boolean('wants_partnership')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_details');
    }
};
