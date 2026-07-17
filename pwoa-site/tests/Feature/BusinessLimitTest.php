<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_listing_can_access_create_flow()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('business.manage', ['create' => 'contractor']));
        $response->assertOk();
    }

    public function test_user_with_listing_cannot_access_create_flow()
    {
        $user = User::factory()->create();
        Business::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('business.manage', ['create' => 'contractor']));
        $response->assertRedirect(route('business.manage'));
        $response->assertSessionHas('error', 'You already have an active business listing.');
    }

    public function test_user_with_listing_can_access_edit_flow()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create(['user_id' => $user->id, 'type' => 'contractor']);

        $response = $this->actingAs($user)->get(route('contractors.edit'));
        $response->assertRedirect(route('business.manage', ['edit' => $business->id]));
    }

    public function test_user_cannot_create_multiple_listings_in_database()
    {
        $user = User::factory()->create();
        Business::factory()->create(['user_id' => $user->id]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        // Try to insert a second business for same user (database unique constraint protection)
        Business::factory()->create(['user_id' => $user->id]);
    }

    public function test_user_can_create_new_listing_after_soft_deleting_old_listing()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create(['user_id' => $user->id]);

        $business->delete(); // Soft delete

        // Now creating a new business should succeed and not throw unique constraint exception
        $newBusiness = Business::factory()->create(['user_id' => $user->id]);
        
        $this->assertNotNull($newBusiness);
        $this->assertEquals($newBusiness->id, $user->fresh()->business->id);
    }

    public function test_edit_mode_has_six_steps_and_starts_at_company_details()
    {
        $user = User::factory()->create();
        $business = Business::factory()->create(['user_id' => $user->id, 'type' => 'contractor']);

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\Business\ManageBusiness::class)
            ->call('editListing', $business->id)
            ->assertSet('isEdit', true)
            ->assertSet('totalSteps', 6)
            ->assertSet('currentStep', 1)
            ->assertSet('name', $business->name);
    }
}
