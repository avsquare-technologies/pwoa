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
            if (!Schema::hasColumn('contractor_details', 'license_path')) {
                $table->string('license_path')->nullable()->after('license_number');
            }
            if (!Schema::hasColumn('contractor_details', 'insurance_path')) {
                $table->string('insurance_path')->nullable()->after('is_insured');
            }
        });

        Schema::table('business_directory_certification', function (Blueprint $table) {
            if (!Schema::hasColumn('business_directory_certification', 'document_path')) {
                $table->string('document_path')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contractor_details', function (Blueprint $table) {
            $table->dropColumn(['license_path', 'insurance_path']);
        });

        Schema::table('business_directory_certification', function (Blueprint $table) {
            $table->dropColumn(['document_path']);
        });
    }
};
