<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Desk;

class DeskFactory extends Factory
{
    protected $model = Desk::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'location' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['available', 'occupied', 'maintenance']),
        ];
    }
}

