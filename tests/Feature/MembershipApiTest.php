<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Desk;
use App\Models\Membership;
use app\Http\Controllers\Api\MembershipController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MembershipApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_own_membership()
    {
        $user = User::factory()->create(['role' => 'user']);
        $desk = Desk::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/memberships', [
            'user_id' => $user->id, // Creating membership for oneself
            'desk_id' => $desk->id,
            'membership_type' => 'monthly',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'price' => 200.00,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('memberships', ['user_id' => $user->id, 'desk_id' => $desk->id]);
    }

    public function test_user_cannot_create_membership_for_others()
    {
        $user = User::factory()->create(['role' => 'user']);
        $otherUser = User::factory()->create();
        $desk = Desk::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/memberships', [
            'user_id' => $otherUser->id, // Attempting to create membership for another user
            'desk_id' => $desk->id,
            'membership_type' => 'monthly',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'price' => 200.00,
        ]);

        $response->assertStatus(403); // A regular user cannot create memberships for others
    }

    public function test_admin_can_create_membership_for_anyone()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $desk = Desk::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/memberships', [
            'user_id' => $user->id, // Admin creates membership for any user
            'desk_id' => $desk->id,
            'membership_type' => 'monthly',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'price' => 200.00,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('memberships', ['user_id' => $user->id, 'desk_id' => $desk->id]);
    }

    public function test_user_can_delete_own_unpaid_membership()
    {
        $user = User::factory()->create(['role' => 'user']);
        $membership = Membership::factory()->create(['user_id' => $user->id, 'price' => 0]); // Unpaid membership

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/memberships/{$membership->id}");

        $response->assertStatus(204); // Successful deletion
        $this->assertDatabaseMissing('memberships', ['id' => $membership->id]);
    }

    public function test_user_cannot_delete_paid_membership()
    {
        $user = User::factory()->create(['role' => 'user']);
        $membership = Membership::factory()->create([
            'user_id' => $user->id,
            'amount_paid' => 100, // Paid
            'payment_status' => 'paid', // Paid status
        ]);
    
        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/memberships/{$membership->id}");
    
        $response->assertStatus(403); // Access denied
    }    

    public function test_user_cannot_delete_others_membership()
    {
        $user = User::factory()->create(['role' => 'user']);
        $otherUser = User::factory()->create();
        $membership = Membership::factory()->create(['user_id' => $otherUser->id, 'price' => 0]); // Another user's membership

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/memberships/{$membership->id}");

        $response->assertStatus(403); // Access denied
    }

    public function test_admin_can_delete_any_membership()
    {
        $admin = User::factory()->create(['role' => 'admin']);
    
        // **Creating paid and unpaid memberships**
        $paidMembership = Membership::factory()->create([
            'amount_paid' => 100, 
            'payment_status' => 'paid'
        ]);
    
        $unpaidMembership = Membership::factory()->create([
            'amount_paid' => 0, 
            'payment_status' => 'pending'
        ]);
    
        // **Admin deletes unpaid membership**
        $responseUnpaid = $this->actingAs($admin, 'sanctum')->deleteJson("/api/v1/memberships/{$unpaidMembership->id}");
        $responseUnpaid->assertStatus(204);
        $this->assertDatabaseMissing('memberships', ['id' => $unpaidMembership->id]);
    
        // **Admin deletes paid membership**
        $responsePaid = $this->actingAs($admin, 'sanctum')->deleteJson("/api/v1/memberships/{$paidMembership->id}");
        $responsePaid->assertStatus(204);
        $this->assertDatabaseMissing('memberships', ['id' => $paidMembership->id]);
    }    

    public function test_user_can_pay_for_membership()
    {
        $user = User::factory()->create();
        $membership = Membership::factory()->create([
            'user_id' => $user->id,
            'amount_paid' => 0, // Initial amount
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($user, 'sanctum')->patchJson("/api/v1/memberships/{$membership->id}/pay", [
            'amount_paid' => 200,
            'payment_status' => 'paid',
            'payment_method' => 'credit_card',
            'transaction_reference' => 'TXN123456',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('memberships', [
            'id' => $membership->id,
            'amount_paid' => 200,
            'payment_status' => 'paid',
        ]);
    }

}
