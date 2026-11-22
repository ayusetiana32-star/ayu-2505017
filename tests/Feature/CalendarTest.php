<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendar_events_include_deadline_course_color_and_detail_url_only_for_current_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $course = Course::factory()->for($user)->create(['color' => '#123ABC']);
        $otherCourse = Course::factory()->for($otherUser)->create();
        $task = Task::factory()->for($course)->create([
            'title' => 'Tugas kalender saya',
            'deadline' => '2026-12-01 14:30:00',
        ]);
        Task::factory()->for($otherCourse)->create([
            'title' => 'Tugas milik pengguna lain',
            'deadline' => '2026-12-02 14:30:00',
        ]);

        $this->actingAs($user)
            ->get(route('calendar'))
            ->assertOk()
            ->assertSee('Kalender Deadline')
            ->assertSee('fullcalendar@6.1.15');

        $this->get(route('tasks.events'))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', (string) $task->id)
            ->assertJsonPath('0.title', 'Tugas kalender saya')
            ->assertJsonPath('0.start', $task->deadline->toIso8601String())
            ->assertJsonPath('0.backgroundColor', '#123ABC')
            ->assertJsonPath('0.borderColor', '#123ABC')
            ->assertJsonPath('0.url', route('tasks.show', $task));
    }

    public function test_calendar_events_open_the_task_detail_and_respect_task_ownership(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['title' => 'Detail kalender']);

        $this->actingAs($task->user)
            ->get(route('tasks.show', $task))
            ->assertOk()
            ->assertSee('Detail kalender');

        $this->actingAs($user)
            ->get(route('tasks.show', $task))
            ->assertForbidden();
    }
}
