<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Region>
 */
class RegionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'x' => $this->faker->randomFloat(2,0,100),
            'y' => $this->faker->randomFloat(2,0,100),
            'lockable' => $this->faker->boolean(),
            'locked' => $this->faker->boolean(),
        ];
    }
}
