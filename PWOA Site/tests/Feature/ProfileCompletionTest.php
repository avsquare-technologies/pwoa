<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use App\Services\ProfileCompletionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileCompletionTest extends TestCase
{
    use RefreshDatabase;

    protected ProfileCompletionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProfileCompletionService();

        // Create country, state, and city to satisfy foreign key constraints
        \App\Models\Country::create([
            'id' => 1,
            'name' => 'United States',
        ]);

        \App\Models\State::create([
            'id' => 1,
            'name' => 'Texas',
            'country_id' => 1,
            'country_code' => 'US',
        ]);

        \App\Models\City::create([
            'id' => 1,
            'name' => 'Austin',
            'state_id' => 1,
            'state_code' => 'TX',
            'country_id' => 1,
            'country_code' => 'US',
            'latitude' => 30.2672,
            'longitude' => -97.7431,
        ]);
    }

    public function test_contractor_profile_completion_calculates_correctly()
    {
        $user = User::factory()->create();
        
        // 1. Weak Profile: Minimal attributes
        $business = Business::factory()->create([
            'user_id' => $user->id,
            'type' => 'contractor',
            'name' => 'Wash Patrol',
            'email' => 'wash@patrol.com',
            'phone' => '1234567890',
            'logo_path' => null,
            'cover_photo_path' => null,
            'address' => null,
            'description' => null,
        ]);

        $data = $this->service->getCompletionData($business);
        
        // Has Company Info (20%), lacks others. Total = 20%
        $this->assertEquals(20, $data['percentage']);
        $this->assertEquals('Incomplete', $data['status_label']);
        $this->assertEquals(1, $data['next_incomplete_edit_step']); // Address is in edit step 1

        // 2. Add Address (10%) and Description (10%)
        $business->update([
            'address' => '123 Test St',
            'city_id' => 1,
            'state_id' => 1,
            'zip' => '12345',
            'description' => 'A great cleaning service that you can trust.',
        ]);
        
        $data = $this->service->getCompletionData($business);
        // Has Company Info (20%) + Address (10%) + Description (10%) = 40%
        $this->assertEquals(40, $data['percentage']);
        $this->assertEquals('Good Progress', $data['status_label']);
    }

    public function test_vendor_profile_completion_has_vendor_specific_weights()
    {
        $user = User::factory()->create();
        
        $business = Business::factory()->create([
            'user_id' => $user->id,
            'type' => 'vendor',
            'name' => 'Wash Parts Vendor',
            'email' => 'parts@wash.com',
            'phone' => '0987654321',
            'logo_path' => 'logos/vendor.png', // Logo (+10%)
            'cover_photo_path' => null,
            'address' => '456 Vendor Blvd',
            'city_id' => 1,
            'state_id' => 1,
            'zip' => '54321',
            'description' => 'Top provider of power washing parts.',
        ]);

        // Has Company Info (20%), Address (10%), Description (10%), Logo (10%). Total = 50%
        $data = $this->service->getCompletionData($business);
        $this->assertEquals(50, $data['percentage']);
        $this->assertEquals('Good Progress', $data['status_label']);

        // Add vendor details feature (20%)
        $business->vendorDetail()->create([
            'years_in_business' => 10,
        ]);

        $data = $this->service->getCompletionData($business);
        // Total should increase by 20% to 70%
        $this->assertEquals(70, $data['percentage']);
        $this->assertEquals('Good Progress', $data['status_label']);
    }
}
