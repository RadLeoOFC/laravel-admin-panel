<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Desk;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeskAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_desk_is_not_available_when_booked()
    {
        // Create a desk with the status set to 'occupied'
        $desk = Desk::factory()->create(['status' => 'occupied']);

        // Check that the desk is not available
        $isAvailable = $desk->status === 'available';

        $this->assertFalse($isAvailable);
    }

    public function test_desk_is_available_by_default()
    {
        // Create a desk without specifying a status
        $desk = Desk::factory()->create();
    
        // Verify that the desk is created with the default status 'available'
        $this->assertEquals('available', $desk->status);
    }
    
}
