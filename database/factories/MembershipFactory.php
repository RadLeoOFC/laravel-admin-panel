<?php

namespace Database\Factories;

use App\Models\Membership;
use App\Models\User;
use App\Models\Desk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Membership>
 */
class MembershipFactory extends Factory
{
    protected $model = Membership::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Create a user and assign its ID
            'desk_id' => Desk::factory(), // Create a desk and assign its ID
            'start_date' => now(),
            'end_date' => null,
            'membership_type' => $this->faker->randomElement(['daily', 'monthly', 'yearly']),
            'price' => $this->faker->randomFloat(2, 10, 500), // Price between 10 and 500
        ];
    }
}
