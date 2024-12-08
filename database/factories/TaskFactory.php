<?php

namespace Database\Factories;

use App\Enums\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $user = User::factory()->create();
        return [
            'user_id' => 1, //$user->id,
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(3),
            'due_date' => Carbon::now()->addDays(rand(1, 365))->format('Y-m-d'),
            'status' => fake()->randomElement(array_column(Status::cases(), 'value')),
        ];
    }
}
