<?php

namespace Database\Seeders;

use App\Models\BusinessCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Course Categories
        $courseCats = [
            ['name' => 'Safety & Compliance', 'slug' => 'safety-compliance'],
            ['name' => 'Technical Skills', 'slug' => 'technical-skills'],
            ['name' => 'Business Management', 'slug' => 'business-management'],
            ['name' => 'Industry Certification', 'slug' => 'industry-certification'],
        ];

        foreach ($courseCats as $cat) {
            \App\Models\CourseCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 2. Event Categories
        $eventCats = [
            ['name' => 'Regional Conference', 'slug' => 'regional-conference'],
            ['name' => 'Technical Workshop', 'slug' => 'technical-workshop'],
            ['name' => 'Community Gathering', 'slug' => 'community-gathering'],
            ['name' => 'Webinar', 'slug' => 'webinar'],
        ];

        foreach ($eventCats as $cat) {
            \App\Models\EventCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 3. Contractor Business Categories
        $contractorData = [
            'Residential Services' => [
                'House Washing', 'Roof Cleaning', 'Driveway Cleaning', 'Patio Cleaning',
                'Pool Deck Cleaning', 'Fence Cleaning', 'Deck Cleaning', 'Paver Cleaning',
                'Solar Panel Cleaning', 'Window Cleaning', 'Gutter Cleaning'
            ],
            'Commercial Services' => [
                'Building Washing', 'Storefront Cleaning', 'Shopping Center Cleaning',
                'Apartment Complex Cleaning', 'HOA Cleaning', 'Parking Garage Cleaning',
                'Fleet Washing', 'Restaurant Cleaning', 'Dumpster Pad Cleaning',
                'Warehouse Cleaning', 'Industrial Cleaning'
            ],
            'Specialty Services' => [
                'Soft Washing', 'Pressure Washing', 'Power Washing', 'Hot Water Cleaning',
                'Graffiti Removal', 'Gum Removal', 'Rust Removal', 'Oil Stain Removal',
                'Concrete Sealing', 'Paver Sealing', 'Wood Restoration', 'Water Recovery'
            ]
        ];

        foreach ($contractorData as $parentName => $children) {
            $parent = BusinessCategory::firstOrCreate(
                ['slug' => Str::slug($parentName), 'type' => 'contractor'],
                [
                    'name' => $parentName,
                    'category_type' => 'parent',
                    'description' => $parentName . ' for the pressure washing industry.',
                ]
            );

            foreach ($children as $childName) {
                BusinessCategory::firstOrCreate(
                    ['slug' => Str::slug($childName), 'type' => 'contractor'],
                    [
                        'name' => $childName,
                        'category_type' => 'child',
                        'parent_id' => $parent->id,
                        'description' => $childName . ' services.',
                    ]
                );
            }
        }

        // 4. Vendor Business Categories
        $vendorData = [
            'Equipment' => [
                'Hot Water Pressure Washers', 'Cold Water Pressure Washers', 'Soft Wash Systems',
                'Pressure Washing Trailers', 'Pressure Washing Skids', 'Surface Cleaners',
                'Water Tanks', 'Hose Reels', 'Pumps', 'Burners', 'Generators', 'Water Recovery Systems'
            ],
            'Parts & Accessories' => [
                'Hoses', 'Guns', 'Wands', 'Tips', 'Nozzles', 'Turbo Nozzles',
                'Downstream Injectors', 'Ball Valves', 'Quick Connects', 'Filters',
                'Unloaders', 'Belts', 'Repair Parts', 'Fittings'
            ],
            'Chemicals & Supplies' => [
                'Sodium Hypochlorite', 'Surfactants', 'Degreasers', 'Rust Removers',
                'Graffiti Removers', 'House Wash Chemicals', 'Roof Wash Chemicals',
                'Fleet Wash Chemicals', 'Concrete Cleaners', 'Sealers'
            ],
            'Business Services' => [
                'Website Design', 'SEO Services', 'PPC Advertising', 'CRM Software',
                'Invoicing Software', 'Estimating Software', 'Call Answering Services',
                'Insurance Services', 'Financing Services', 'Bookkeeping', 'Legal Services'
            ],
            'Training & Education' => [
                'Pressure Washing Training', 'Soft Washing Training', 'Roof Cleaning Training',
                'Environmental Compliance Training', 'Business Coaching', 'Certification Programs'
            ],
            'Manufacturers' => [
                'Equipment Manufacturer', 'Chemical Manufacturer', 'Parts Manufacturer',
                'Trailer Manufacturer', 'Software Provider'
            ]
        ];

        foreach ($vendorData as $parentName => $children) {
            $parent = BusinessCategory::firstOrCreate(
                ['slug' => Str::slug($parentName), 'type' => 'vendor'],
                [
                    'name' => $parentName,
                    'category_type' => 'parent',
                    'description' => $parentName . ' products and services.',
                ]
            );

            foreach ($children as $childName) {
                BusinessCategory::firstOrCreate(
                    ['slug' => Str::slug($childName), 'type' => 'vendor'],
                    [
                        'name' => $childName,
                        'category_type' => 'child',
                        'parent_id' => $parent->id,
                        'description' => $childName . ' products.',
                    ]
                );
            }
        }
    }
}

