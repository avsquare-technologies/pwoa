<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MembershipStatus;
use App\Services\WashBalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessControlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an inactive member is redirected when attempting to access the dashboard.
     */
    public function test_inactive_member_is_redirected_to_subscribe(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
        ]);
        // Do not create an active membership status

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertRedirect(route('membership.subscribe_form'));
    }

    /**
     * Test that an active member without premium balance is directed to the upgrade page on restricted cards.
     */
    public function test_active_member_without_premium_balance_sees_upgrade_gating(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
        ]);

        // Make user an active member
        MembershipStatus::create([
            'user_id' => $user->id,
            'is_active' => true,
            'plan' => 'gold',
            'started_at' => now(),
        ]);

        // Mock WashBalanceService to return false (insufficient balance)
        $this->mock(WashBalanceService::class, function ($mock) {
            $mock->shouldReceive('hasRequiredBalance')
                ->andReturn(false);
            $mock->shouldReceive('getBalance')
                ->andReturn(500.0); // Less than 2000
        });

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();

        // Assert that the dashboard contains gating upgrade links instead of the direct feature routes
        $response->assertSee(route('wash.upgrade'));
        $response->assertSee('Unlock Events');
        $response->assertSee('Upgrade'); // The lock badge text
    }

    /**
     * Test that a premium user has full access to the actual routes on the dashboard.
     */
    public function test_premium_user_has_direct_links_on_dashboard(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
        ]);

        // Make user an active member
        MembershipStatus::create([
            'user_id' => $user->id,
            'is_active' => true,
            'plan' => 'gold',
            'started_at' => now(),
        ]);

        // Mock WashBalanceService to return true (sufficient balance)
        $this->mock(WashBalanceService::class, function ($mock) {
            $mock->shouldReceive('hasRequiredBalance')
                ->andReturn(true);
            $mock->shouldReceive('getBalance')
                ->andReturn(2500.0); // More than 2000
        });

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();

        // Assert that premium links are active
        $response->assertSee(route('courses'));
        $response->assertSee(route('events'));
        $response->assertSee(route('certificates.index'));
        $response->assertSee(route('complaints.index'));
        $response->assertSee('Browse Events'); // Normal empty NFT ticket state button
        $response->assertDontSee('Unlock Events & Tickets');
    }
}
