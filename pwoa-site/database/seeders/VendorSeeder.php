<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $category = BusinessCategory::where('slug', 'hot-water-pressure-washers')->first();

        $vendors = [

            [
                'name' => 'FNA GROUP',
                'website' => 'https://www.fna-group.com',
                'email' => 'info@fna-group.com',
                'description' => 'Professional pressure washing equipment manufacturer and supplier.',
            ],

            [
                'name' => 'BARENS',
                'website' => 'https://www.barens.com',
                'email' => 'info@barens.com',
                'description' => 'Industrial cleaning equipment and pressure washer systems provider.',
            ],

        ];

        foreach ($vendors as $vendor) {

            $user = User::factory()->create([
                'name' => $vendor['name'],
                'email' => $vendor['email'],
            ]);

            $business = Business::create([
                'user_id' => $user->id,
                'name' => $vendor['name'],
                'slug' => Str::slug($vendor['name']),
                'type' => 'vendor',
                'status' => 'approved',

                'website' => $vendor['website'],
                'email' => $vendor['email'],
                'description' => $vendor['description'],

                'membership_tier' => 'gold',
                'is_verified' => true,
                'is_preferred' => true,

                'cover_photo_path' => null,
                'logo_path' => null,
            ]);

            if ($category) {
                $business->categories()->sync([$category->id]);
            }

            $business->vendorDetail()->create([
                'years_in_business' => 10,
                'has_online_ordering' => true,
                'has_local_pickup' => true,
                'has_member_discounts' => true,
                'wants_preferred_program' => true,
                'wants_partnership' => true,
            ]);
        }
    }
}
