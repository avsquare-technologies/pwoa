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
        Schema::table('businesses', function (Blueprint $table) {
            // Add soft deletes column if it doesn't exist
            if (!Schema::hasColumn('businesses', 'deleted_at')) {
                $table->softDeletes();
            }

            // Add active_user_id virtual column
            if (!Schema::hasColumn('businesses', 'active_user_id')) {
                $table->unsignedBigInteger('active_user_id')
                    ->nullable()
                    ->virtualAs('CASE WHEN deleted_at IS NULL THEN user_id ELSE NULL END');
            }

            // Add unique index on active_user_id
            $table->unique('active_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            try {
                $table->dropUnique(['active_user_id']);
            } catch (\Throwable $e) {}

            if (Schema::hasColumn('businesses', 'active_user_id')) {
                $table->dropColumn('active_user_id');
            }

            if (Schema::hasColumn('businesses', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
};
