<?php

namespace Modules\Task\Database\Factories;

use App\Models\Action;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Task\Models\Task::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'action_id' => Action::inRandomOrder()->first()->id,
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'order' => $this->faker->randomDigit(),
            'score' => $this->faker->randomDigit(),
            'duration' => $this->faker->randomDigit(),
        ];
    }
}

