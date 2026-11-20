<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Reminder;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_and_reminder_factories_create_related_records_with_datetime_casts(): void
    {
        $task = Task::factory()->create();
        $reminder = Reminder::factory()->for($task)->create();

        $this->assertSame($task->course->user_id, $task->user_id);
        $this->assertTrue($task->user->is($task->course->user));
        $this->assertTrue($task->reminders->contains($reminder));
        $this->assertTrue($reminder->task->is($task));
        $this->assertInstanceOf(Carbon::class, $task->deadline);
        $this->assertInstanceOf(Carbon::class, $reminder->remind_at);
        $this->assertNull($reminder->sent_at);
    }

    public function test_deleting_a_user_cascades_to_courses_tasks_and_reminders(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->for($user)->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
        $reminder = Reminder::factory()->for($task)->create();

        $user->delete();

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        $this->assertDatabaseMissing('reminders', ['id' => $reminder->id]);
    }
}
