<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\City;
use App\Models\State;
use App\Models\User;
use App\Models\DirectoryCertification;
use App\Models\DirectoryEquipment;
use App\Models\ServiceRadius;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $contractorCats = BusinessCategory::where('type', 'contractor')->where('category_type', 'child')->pluck('id')->toArray();
        $vendorCats = BusinessCategory::where('type', 'vendor')->where('category_type', 'child')->pluck('id')->toArray();

        // Get lookup templates
        $certifications = DirectoryCertification::all();
        $equipments = DirectoryEquipment::all();
        $radii = ServiceRadius::all();

        // Sample Premium Image Pools (Unsplash High-Res)
        $banners = [
            'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1628177142898-93e36e4e3a50?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1521791136064-7986c2959c99?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1517646280104-a6feef01477a?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1504307651254-35680f3366d4?auto=format&fit=crop&w=1200&q=80',
        ];

        $logos = [
            'https://api.dicebear.com/7.x/identicon/svg?seed=',
            'https://api.dicebear.com/7.x/initials/svg?seed=',
            'https://api.dicebear.com/7.x/shapes/svg?seed=',
        ];

        $cities = City::limit(10)->get();

        // 1. Create 20 Vendors
        for ($i = 1; $i <= 20; $i++) {
            $email = 'vendor' . $i . '_' . time() . '@example.com';
            $user = User::factory()->create([
                'name' => 'Vendor User ' . $i,
                'email' => $email,
            ]);

            $bizName = 'Premium Vendor ' . $i;
            $city = $cities->isNotEmpty() ? $cities->random() : null;
            $stateId = $city?->state_id ?? 1;

            $business = Business::create([
                'user_id' => $user->id,
                'name' => $bizName,
                'slug' => Str::slug($bizName) . '-' . Str::random(5),
                'type' => 'vendor',
                'status' => 'approved',
                'description' => '<p>High quality equipment and chemicals supplier representing Premium Vendor ' . $i . '.</p>',
                'email' => $email,
                'phone' => '(800) 555-' . sprintf('%04d', $i),
                'website' => 'https://vendor' . $i . 'demo.com',
                'country_id' => 1,
                'state_id' => $stateId,
                'city_id' => $city?->id,
                'address' => $i . '23 Industry Parkway',
                'zip' => '85001',
                'membership_tier' => $i % 3 === 0 ? 'gold' : 'standard',
                'is_verified' => $i % 4 === 0,
                'is_preferred' => $i % 5 === 0,
                'cover_photo_path' => $banners[array_rand($banners)],
                'logo_path' => $logos[array_rand($logos)] . urlencode($bizName),
                'views_count' => rand(50, 500),
            ]);

            // Sync random categories
            if (!empty($vendorCats)) {
                $business->categories()->sync((array) $vendorCats[array_rand($vendorCats)]);
            }

            // Create Vendor Details
            $business->vendorDetail()->create([
                'years_in_business' => rand(2, 25),
                'has_online_ordering' => (bool) rand(0, 1),
                'has_local_pickup' => (bool) rand(0, 1),
                'has_member_discounts' => (bool) rand(0, 1),
                'wants_preferred_program' => (bool) rand(0, 1),
                'wants_partnership' => (bool) rand(0, 1),
            ]);
        }

        // 2. Create 25 Contractors
        for ($i = 1; $i <= 25; $i++) {
            $email = 'contractor' . $i . '_' . time() . '@example.com';
            $user = User::factory()->create([
                'name' => 'Contractor User ' . $i,
                'email' => $email,
            ]);

            $bizName = 'Master Contractor ' . $i;
            $city = $cities->isNotEmpty() ? $cities->random() : null;
            $stateId = $city?->state_id ?? 1;

            $business = Business::create([
                'user_id' => $user->id,
                'name' => $bizName,
                'slug' => Str::slug($bizName) . '-' . Str::random(5),
                'type' => 'contractor',
                'status' => 'approved',
                'description' => '<p>Professional pressure washing services from Master Contractor ' . $i . '. We specialize in exterior cleaning, roof washing, and concrete sealing.</p>',
                'email' => $email,
                'phone' => '(602) 555-' . sprintf('%04d', $i),
                'website' => 'https://contractor' . $i . 'demo.com',
                'country_id' => 1,
                'state_id' => $stateId,
                'city_id' => $city?->id,
                'address' => $i . '45 Wash Way',
                'zip' => '85002',
                'membership_tier' => $i % 3 === 0 ? 'gold' : 'standard',
                'is_verified' => $i % 2 === 0,
                'cover_photo_path' => $banners[array_rand($banners)],
                'logo_path' => $logos[array_rand($logos)] . urlencode($bizName),
                'views_count' => rand(50, 600),
            ]);

            // Sync random categories
            if (!empty($contractorCats)) {
                $business->categories()->sync((array) array_slice($contractorCats, 0, rand(1, 3)));
            }

            // Create Contractor Details
            $business->contractorDetail()->create([
                'years_in_business' => rand(1, 15),
                'license_number' => 'AZ-' . rand(100000, 999999),
                'is_insured' => (bool) rand(0, 1),
                'service_radius_id' => $radii->isNotEmpty() ? $radii->random()->id : null,
                'is_emergency_service' => (bool) rand(0, 1),
                'is_subcontracting' => (bool) rand(0, 1),
                'is_national_accounts' => (bool) rand(0, 1),
            ]);

            // Sync random Certifications & Equipment
            if ($certifications->isNotEmpty()) {
                $countToPick = min($certifications->count(), rand(1, 2));
                $business->directoryCertifications()->sync(
                    $certifications->random($countToPick)->pluck('id')->toArray()
                );
            }

            if ($equipments->isNotEmpty()) {
                $equipSyncData = [];
                $countToPick = min($equipments->count(), rand(1, 3));
                $selectedEquips = $equipments->random($countToPick);
                foreach ($selectedEquips as $eq) {
                    $equipSyncData[$eq->id] = [
                        'quantity' => rand(1, 5),
                        'specifications' => rand(0, 1) ? 'Commercial Grade' : null,
                    ];
                }
                $business->directoryEquipments()->sync($equipSyncData);
            }
        }
    }
}

