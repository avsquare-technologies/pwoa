<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\MembershipStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontEventControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an active user with active membership
        $this->user = User::factory()->create([
            'status' => 'active',
        ]);

        MembershipStatus::create([
            'user_id' => $this->user->id,
            'is_active' => true,
            'plan' => 'gold',
            'started_at' => now()->subDays(5),
            'expires_at' => now()->addDays(30),
        ]);
    }

    public function test_detail_redirects_by_numeric_id_to_seo_slug_page(): void
    {
        $event = Event::create([
            'title' => 'PWOA Early Admission Member Onboarding',
            'slug' => 'pwoa-early-admission-member-onboarding',
            'starts_at' => now()->addDays(1),
            'ends_at' => now()->addDays(1)->addHours(2),
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('events.detail', $event->id));

        $response->assertRedirect(route('events.show', $event->slug));
    }

    public function test_detail_redirects_by_slug_to_seo_slug_page(): void
    {
        $event = Event::create([
            'title' => 'PWOA Early Admission Member Onboarding',
            'slug' => 'pwoa-early-admission-member-onboarding',
            'starts_at' => now()->addDays(1),
            'ends_at' => now()->addDays(1)->addHours(2),
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('events.detail', $event->slug));

        $response->assertRedirect(route('events.show', $event->slug));
    }

    public function test_detail_redirects_invalid_event_to_index_with_error(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('events.detail', 'non-existent-event'));

        $response->assertRedirect(route('events.index'));
        $response->assertSessionHas('error', 'Event not found or is no longer available.');
    }

    public function test_detail_redirects_unpublished_event_to_index_with_error(): void
    {
        $event = Event::create([
            'title' => 'Draft Event',
            'slug' => 'draft-event',
            'starts_at' => now()->addDays(1),
            'ends_at' => now()->addDays(1)->addHours(2),
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('events.detail', $event->slug));

        $response->assertRedirect(route('events.index'));
        $response->assertSessionHas('error', 'Event not found or is no longer available.');
    }
}
