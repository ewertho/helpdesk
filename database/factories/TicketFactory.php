<?php

namespace Database\Factories;

use App\Enums\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subject' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'category' => Category::Other,
            'requester_id' => User::factory(),
        ];
    }
}
