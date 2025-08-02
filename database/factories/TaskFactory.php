<?php

namespace Database\Factories;

use App\Enums\Task\TaskStatusEnum;
use App\Infrastructure\Persistence\Model\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistence\Model\Task>
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
        return [
            'id' => Str::uuid()->toString(),
            'user_id' => User::factory()->createOne(),
            'title' => fake()->name(),
            'description' => fake()->text(),
            'status' => TaskStatusEnum::TODO,
        ];
    }
}
