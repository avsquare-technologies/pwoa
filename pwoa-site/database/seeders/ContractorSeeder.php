<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContractorSeeder extends Seeder
{
    public function run(): void
    {
        $category = BusinessCategory::where('slug', 'pressure-washing')->first();
        $pwoaCert = \App\Models\DirectoryCertification::where('slug', 'pwoa-certified')->first();

        $contractors = [

            [
                'name' => 'Wash Patrol Phoenix',
                'email' => 'info@washpatrol.com',
                'phone' => '(602) 922-3333',
                'city' => 'Phoenix',
                'state' => 'Arizona',
                'address' => '2338 N. 53rd St.',
                'website' => 'https://washpatrol.com',
                'owner' => 'Ty Schell',
                'banner_path' => 'contractors/wash-patrol-phoenix-banner.jpg',
                'logo_path' => 'contractors/wash-patrol-logo.png',
                'description' => 'Premier power washing company in Phoenix, AZ.',
            ],

            [
                'name' => 'Wash Patrol Gilbert',
                'email' => 'gilbert@washpatrol.com',
                'phone' => '(480) 741-4333',
                'city' => 'Gilbert',
                'state' => 'Arizona',
                'address' => '3921 S Angler Drive',
                'website' => 'https://washpatrol.com',
                'owner' => 'Tony Gonzales',
                'banner_path' => 'contractors/wash-patrol-gilbert-banner.jpg',
                'logo_path' => 'contractors/wash-patrol-logo.png',
                'description' => 'Professional residential and commercial exterior cleaning services.',
            ],

            [
                'name' => 'Wash Patrol Scottsdale',
                'email' => 'scottsdale@washpatrol.com',
                'phone' => '(480) 500-0332',
                'city' => 'Scottsdale',
                'state' => 'Arizona',
                'address' => '1715 N Miller Rd',
                'website' => 'https://washpatrol.com',
                'owner' => 'Dylan Claybourn',
                'banner_path' => 'contractors/wash-patrol-scottsdale-banner.jpg',
                'logo_path' => 'contractors/wash-patrol-logo.png',
                'description' => 'Residential and commercial pressure washing services in Scottsdale.',
            ],

        ];

        foreach ($contractors as $contractor) {

            $user = User::factory()->create([
                'name' => $contractor['owner'],
                'email' => $contractor['email'],
            ]);

            $business = Business::create([
                'user_id' => $user->id,
                'name' => $contractor['name'],
                'slug' => Str::slug($contractor['name']),
                'type' => 'contractor',
                'status' => 'approved',

                'email' => $contractor['email'],
                'phone' => $contractor['phone'],
                'website' => $contractor['website'],

                'country_id' => 1,
                'state_id' => 1,
                'city_id' => 1,

                'address' => $contractor['address'],

                'cover_photo_path' => $contractor['banner_path'],
                'logo_path' => $contractor['logo_path'],

                'description' => $contractor['description'],

                'membership_tier' => 'gold',
                'is_verified' => true,
            ]);

            if ($category) {
                $business->categories()->sync([$category->id]);
            }

            $business->contractorDetail()->create([
                'years_in_business' => 5,
                'license_number' => 'AZ-' . rand(100000, 999999),
                'is_insured' => true,
                'is_emergency_service' => true,
                'is_subcontracting' => false,
                'is_national_accounts' => true,
            ]);

            if ($pwoaCert) {
                $business->directoryCertifications()->sync([$pwoaCert->id]);
            }
        }
    }
}
