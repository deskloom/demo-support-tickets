<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(),
            'status' => 'open',
            'priority' => 'normal',
            'assignee_name' => null,
        ];
    }
}
