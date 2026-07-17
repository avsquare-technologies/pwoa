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
        Schema::table('contractor_details', function (Blueprint $table) {
            if (!Schema::hasColumn('contractor_details', 'license_status')) {
                $table->string('license_status')->default('pending')->after('license_path');
            }
            if (!Schema::hasColumn('contractor_details', 'insurance_status')) {
                $table->string('insurance_status')->default('pending')->after('insurance_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contractor_details', function (Blueprint $table) {
            $table->dropColumn(['license_status', 'insurance_status']);
        });
    }
};
