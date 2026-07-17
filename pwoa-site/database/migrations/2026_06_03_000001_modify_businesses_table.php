<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Safe dropping of foreign key business_category_id
        try {
            Schema::table('businesses', function (Blueprint $table) {
                $table->dropForeign(['business_category_id']);
            });
        } catch (\Throwable $e) {
            // Ignore if foreign key doesn't exist
        }

        // 2. Safe dropping of foreign key user_id
        try {
            Schema::table('businesses', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (\Throwable $e) {
            // Ignore if foreign key doesn't exist
        }

        // 3. Safe dropping of unique constraint user_id
        try {
            Schema::table('businesses', function (Blueprint $table) {
                $table->dropUnique(['user_id']);
            });
        } catch (\Throwable $e) {
            // Ignore if unique index doesn't exist
        }

        // 4. Safe dropping of index user_id if it exists
        try {
            Schema::table('businesses', function (Blueprint $table) {
                $table->dropIndex(['user_id']);
            });
        } catch (\Throwable $e) {
            // Ignore if index doesn't exist
        }

        // 5. Update existing NULL membership_tier to 'standard' before altering to NOT NULL
        try {
            DB::table('businesses')->whereNull('membership_tier')->update(['membership_tier' => 'standard']);
            DB::table('businesses')->whereNull('type')->update(['type' => 'contractor']);
            DB::table('businesses')->whereNull('status')->update(['status' => 'pending']);
        } catch (\Throwable $e) {
            // Ignore if tables/columns don't exist yet
        }

        $isSqlite = Schema::getConnection()->getDriverName() === 'sqlite';

        if (!$isSqlite) {
            try {
                Schema::table('businesses', function (Blueprint $table) {
                    $table->dropIndex('businesses_category_index');
                });
            } catch (\Throwable $e) {}

            try {
                Schema::table('businesses', function (Blueprint $table) {
                    $table->dropIndex(['category']);
                });
            } catch (\Throwable $e) {}
        }

        Schema::table('businesses', function (Blueprint $table) use ($isSqlite) {
            // Drop Category & obsolete columns if they exist (skip if sqlite to prevent index errors)
            if (!$isSqlite) {
                if (Schema::hasColumn('businesses', 'business_category_id')) {
                    $table->dropColumn('business_category_id');
                }
                if (Schema::hasColumn('businesses', 'category')) {
                    $table->dropColumn('category');
                }
                if (Schema::hasColumn('businesses', 'service_types')) {
                    $table->dropColumn('service_types');
                }
                if (Schema::hasColumn('businesses', 'is_pwoa_certified')) {
                    $table->dropColumn('is_pwoa_certified');
                }
            }

            // Re-create foreign key and a normal index (no unique)
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index('user_id');

            // Convert type & status columns to string
            $table->string('type')->default('contractor')->change();
            $table->string('status')->default('pending')->change();

            // Rename banner_path to cover_photo_path if banner_path exists and cover_photo_path does not
            if (Schema::hasColumn('businesses', 'banner_path') && !Schema::hasColumn('businesses', 'cover_photo_path')) {
                $table->renameColumn('banner_path', 'cover_photo_path');
            } elseif (Schema::hasColumn('businesses', 'banner_path') && Schema::hasColumn('businesses', 'cover_photo_path')) {
                $table->dropColumn('banner_path');
            } elseif (!Schema::hasColumn('businesses', 'cover_photo_path')) {
                $table->string('cover_photo_path')->nullable()->after('logo_path');
            }

            // Adjust membership_tier default
            $table->string('membership_tier')->default('standard')->change();

            // Add new directory columns if they do not exist
            if (!Schema::hasColumn('businesses', 'short_description')) {
                $table->text('short_description')->nullable()->after('tagline');
            }
            if (!Schema::hasColumn('businesses', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('membership_tier');
            }
            if (!Schema::hasColumn('businesses', 'is_preferred')) {
                $table->boolean('is_preferred')->default(false)->after('is_verified');
            }
            if (!Schema::hasColumn('businesses', 'views_count')) {
                $table->unsignedInteger('views_count')->default(0)->after('is_preferred');
            }

            // Add social media columns if they do not exist
            if (!Schema::hasColumn('businesses', 'facebook')) {
                $table->string('facebook')->nullable()->after('views_count');
            }
            if (!Schema::hasColumn('businesses', 'instagram')) {
                $table->string('instagram')->nullable()->after('facebook');
            }
            if (!Schema::hasColumn('businesses', 'linkedin')) {
                $table->string('linkedin')->nullable()->after('instagram');
            }
            if (!Schema::hasColumn('businesses', 'youtube')) {
                $table->string('youtube')->nullable()->after('linkedin');
            }
            if (!Schema::hasColumn('businesses', 'tiktok')) {
                $table->string('tiktok')->nullable()->after('youtube');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe dropping of foreign key user_id first
        try {
            Schema::table('businesses', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (\Throwable $e) {
            // Ignore
        }

        // Safe dropping of index user_id
        try {
            Schema::table('businesses', function (Blueprint $table) {
                $table->dropIndex(['user_id']);
            });
        } catch (\Throwable $e) {
            // Ignore
        }

        // Restore unique constraint and foreign key
        try {
            Schema::table('businesses', function (Blueprint $table) {
                $table->unique(['user_id']);
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        } catch (\Throwable $e) {
            // Ignore
        }

        Schema::table('businesses', function (Blueprint $table) {
            // Re-create business_category_id
            if (!Schema::hasColumn('businesses', 'business_category_id')) {
                $table->foreignId('business_category_id')->nullable()->constrained('business_categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('businesses', 'category')) {
                $table->string('category')->nullable()->index();
            }
            if (!Schema::hasColumn('businesses', 'service_types')) {
                $table->json('service_types')->nullable();
            }
            if (!Schema::hasColumn('businesses', 'is_pwoa_certified')) {
                $table->boolean('is_pwoa_certified')->default(false);
            }

            // Rename cover_photo_path back to banner_path only if cover_photo_path exists and banner_path does not
            if (Schema::hasColumn('businesses', 'cover_photo_path') && !Schema::hasColumn('businesses', 'banner_path')) {
                $table->renameColumn('cover_photo_path', 'banner_path');
            }

            // Drop added columns
            $table->dropColumn([
                'short_description',
                'is_verified',
                'is_preferred',
                'views_count',
                'facebook',
                'instagram',
                'linkedin',
                'youtube',
                'tiktok'
            ]);
        });
    }
};
