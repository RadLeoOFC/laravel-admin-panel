<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Desk;
use App\Models\Membership;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MembershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_membership_can_be_created()
    {
        // Create a test user
        $user = User::factory()->create();
        
        // Create a desk with 'available' status
        $desk = Desk::factory()->create(['status' => 'available']);

        // Authenticate the user and create a membership
        $response = $this->actingAs($user)->post(route('memberships.store'), [
            'user_id' => $user->id,
            'desk_id' => $desk->id,
            'membership_type' => 'monthly',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'price' => 200
        ]);

        // Expect a redirect (302) to the membership list page
        $response->assertRedirect(route('memberships.index'));

        // Verify that the record exists in the database
        $this->assertDatabaseHas('memberships', [
            'user_id' => $user->id,
            'desk_id' => $desk->id,
            'membership_type' => 'monthly',
        ]);

        // Ensure that the redirect actually leads to the correct page
        $this->followRedirects($response)->assertSee('Memberships');
    }

    public function test_cannot_create_membership_when_desk_is_already_booked()
    {
        // Create two users
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create a desk
        $desk = Desk::factory()->create(['status' => 'available']);

        // The first user books the desk
        $this->actingAs($user1)->post(route('memberships.store'), [
            'user_id' => $user1->id,
            'desk_id' => $desk->id,
            'membership_type' => 'monthly',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'price' => 200
        ])->assertRedirect(route('memberships.index'));

        // Verify that the membership has been added
        $this->assertDatabaseHas('memberships', [
            'user_id' => $user1->id,
            'desk_id' => $desk->id,
        ]);

        // The second user tries to book the same desk
        $response = $this->actingAs($user2)->post(route('memberships.store'), [
            'user_id' => $user2->id,
            'desk_id' => $desk->id,
            'membership_type' => 'monthly',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'price' => 200
        ]);

        // Expect a redirect back to the form with an error message
        $response->assertSessionHasErrors();

        // Ensure that there is only ONE membership for this desk in the database
        $this->assertEquals(1, Membership::where('desk_id', $desk->id)->count());
    }
}
