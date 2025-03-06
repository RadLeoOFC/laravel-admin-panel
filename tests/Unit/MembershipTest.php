<?php

use App\Models\User;
use App\Models\Membership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipTest extends TestCase
{
    use RefreshDatabase; // Clearing the database before each test

    public function test_membership_belongs_to_user()
    {
        // Creating a user
        $user = User::factory()->create();

        // Creating a membership related to the user
        $membership = Membership::factory()->create(['user_id' => $user->id]);

        // Checking if the relationship is working
        $this->assertInstanceOf(User::class, $membership->user);
        $this->assertEquals($user->id, $membership->user->id);
    }
}
