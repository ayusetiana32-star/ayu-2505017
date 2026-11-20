<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement([
                'Algoritma dan Pemrograman',
                'Struktur Data',
                'Basis Data',
                'Jaringan Komputer',
            ]),
            'code' => fake()->unique()->bothify('IF-###'),
            'lecturer' => fake()->name(),
            'color' => fake()->hexColor(),
        ];
    }
}
