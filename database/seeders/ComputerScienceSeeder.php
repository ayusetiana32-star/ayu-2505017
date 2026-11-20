<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ComputerScienceSeeder extends Seeder
{
    public function run(): void
    {
        $student = User::query()->firstOrCreate(
            ['email' => 'ayu.pratama@example.test'],
            [
                'name' => 'Ayu Pratama',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $courses = [
            [
                'name' => 'Algoritma dan Pemrograman',
                'code' => 'IF101',
                'lecturer' => 'Dr. Budi Santoso',
                'color' => '#2563EB',
                'tasks' => [
                    [
                        'title' => 'Implementasi algoritma sorting',
                        'description' => 'Implementasikan insertion sort dan merge sort, lalu bandingkan kompleksitasnya.',
                        'days' => 3,
                        'priority' => 'tinggi',
                        'status' => 'belum',
                    ],
                    [
                        'title' => 'Latihan rekursi',
                        'description' => 'Selesaikan latihan rekursi dan dokumentasikan langkah penyelesaiannya.',
                        'days' => 7,
                        'priority' => 'sedang',
                        'status' => 'proses',
                    ],
                ],
            ],
            [
                'name' => 'Struktur Data',
                'code' => 'IF202',
                'lecturer' => 'Maya Kusuma, M.Kom.',
                'color' => '#16A34A',
                'tasks' => [
                    [
                        'title' => 'Praktikum linked list',
                        'description' => 'Buat operasi tambah, hapus, dan cari pada singly linked list.',
                        'days' => 5,
                        'priority' => 'sedang',
                        'status' => 'belum',
                    ],
                ],
            ],
            [
                'name' => 'Basis Data',
                'code' => 'IF203',
                'lecturer' => 'Rina Lestari, M.T.',
                'color' => '#9333EA',
                'tasks' => [
                    [
                        'title' => 'Rancangan ERD sistem perpustakaan',
                        'description' => 'Susun ERD dan normalisasi skema hingga bentuk normal ketiga.',
                        'days' => -2,
                        'priority' => 'rendah',
                        'status' => 'selesai',
                        'completed_days_ago' => 3,
                    ],
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $tasks = $courseData['tasks'];
            unset($courseData['tasks']);

            $course = $student->courses()->firstOrCreate(
                ['code' => $courseData['code']],
                $courseData,
            );

            foreach ($tasks as $taskData) {
                $days = $taskData['days'];
                $completedDaysAgo = $taskData['completed_days_ago'] ?? null;
                unset($taskData['days'], $taskData['completed_days_ago']);

                $task = $student->tasks()->firstOrCreate(
                    [
                        'course_id' => $course->id,
                        'title' => $taskData['title'],
                    ],
                    [
                        ...$taskData,
                        'deadline' => now()->addDays($days),
                        'completed_at' => $completedDaysAgo === null
                            ? null
                            : now()->subDays($completedDaysAgo),
                    ],
                );

                $task->reminders()->firstOrCreate(
                    ['channel' => 'email'],
                    [
                        'remind_at' => $task->deadline->copy()->subDay(),
                        'sent_at' => $task->status === 'selesai' ? now() : null,
                    ],
                );
            }
        }
    }
}
