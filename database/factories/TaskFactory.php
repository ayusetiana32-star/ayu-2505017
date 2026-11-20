<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'user_id' => fn (array $attributes) => Course::query()
                ->findOrFail($attributes['course_id'])
                ->user_id,
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'deadline' => fake()->dateTimeBetween('now', '+2 months'),
            'priority' => fake()->randomElement(['rendah', 'sedang', 'tinggi']),
            'status' => 'belum',
            'completed_at' => null,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'proses',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'selesai',
            'completed_at' => now(),
        ]);
    }
}
