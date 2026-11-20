<?php

namespace Database\Factories;

use App\Models\Reminder;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reminder>
 */
class ReminderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'remind_at' => fake()->dateTimeBetween('now', '+2 months'),
            'sent_at' => null,
            'channel' => 'email',
        ];
    }
}
