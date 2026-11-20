<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_counts_only_the_authenticated_users_unfinished_tasks(): void
    {
        $this->travelTo(Carbon::parse('2026-11-20 12:00:00'));

        $user = User::factory()->create();
        $course = Course::factory()->for($user)->create();
        $otherUser = User::factory()->create();
        $otherCourse = Course::factory()->for($otherUser)->create();

        $overdue = $this->createTask($user, $course, 'Tugas terlambat', now()->subDay());
        $today = $this->createTask($user, $course, 'Tugas hari ini', now()->addHours(2));
        $nextSevenDays = $this->createTask($user, $course, 'Tugas pekan ini', now()->addDays(2));
        $this->createTask($user, $course, 'Tugas pekan depan', now()->addDays(8));
        $this->createTask($user, $course, 'Tugas selesai', now()->subDay(), 'selesai');
        $this->createTask($otherUser, $otherCourse, 'Tugas user lain', now()->addDay());

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertViewHas('stats', fn (array $stats) => $stats === [
                'unfinished' => 4,
                'overdue' => 1,
                'today' => 1,
                'next_seven_days' => 1,
            ])
            ->assertViewHas('deadlineTasks', function ($tasks) use ($overdue, $today, $nextSevenDays) {
                return $tasks->modelKeys() === [$overdue->id, $today->id, $nextSevenDays->id];
            });
    }

    public function test_task_time_remaining_is_indonesian_and_badges_match_deadline_urgency(): void
    {
        $this->travelTo(Carbon::parse('2026-11-20 12:00:00'));

        $user = User::factory()->create();
        $course = Course::factory()->for($user)->create();

        $late = $this->createTask($user, $course, 'Terlambat', now()->subDay());
        $today = $this->createTask($user, $course, 'Hari ini', now()->addHours(2));
        $soon = $this->createTask($user, $course, 'Segera', now()->addDays(2));
        $safe = $this->createTask($user, $course, 'Aman', now()->addDays(5));

        $this->assertSame('Terlambat 1 hari', $late->time_remaining);
        $this->assertSame('2 hari lagi', $soon->time_remaining);
        $this->assertSame('bg-red-100 text-red-800', $late->deadline_badge_class);
        $this->assertSame('bg-red-100 text-red-800', $today->deadline_badge_class);
        $this->assertSame('bg-amber-100 text-amber-800', $soon->deadline_badge_class);
        $this->assertSame('bg-green-100 text-green-800', $safe->deadline_badge_class);
    }

    private function createTask(
        User $user,
        Course $course,
        string $title,
        Carbon $deadline,
        string $status = 'belum',
    ): Task {
        return Task::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'title' => $title,
            'deadline' => $deadline,
            'status' => $status,
            'completed_at' => $status === 'selesai' ? now() : null,
        ]);
    }
}
