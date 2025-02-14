<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Desk;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Desk>
 */
class DeskFactory extends Factory
{
    protected $model = Desk::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(), // Generate a random desk name
            'location' => $this->faker->sentence(3), // Generate a random location
            'status' => $this->faker->randomElement(['available', 'occupied', 'maintenance']), // Random status
        ];
    }
}


