<?php

namespace Tests\Feature;

use App\Notifications\TaskDeadlineReminderNotification;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TaskDeadlineRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_each_deadline_reminder_once(): void
    {
        Notification::fake();
        $task = Task::factory()->create([
            'deadline' => Carbon::parse('2026-11-22 12:00:00'),
        ]);
        $user = $task->user;

        $this->travelTo(Carbon::parse('2026-11-19 09:00:00'));
        $this->artisan('tasks:send-reminders')->assertSuccessful();
        $this->artisan('tasks:send-reminders')->assertSuccessful();

        Notification::assertSentTo($user, TaskDeadlineReminderNotification::class, function (
            TaskDeadlineReminderNotification $notification,
        ) use ($user): bool {
            $mail = $notification->toMail($user);

            return $notification->daysRemaining === 3
                && $mail->to[0]['address'] === $user->email;
        });
        $this->assertDatabaseCount('reminders', 1);

        $this->travelTo(Carbon::parse('2026-11-21 09:00:00'));
        $this->artisan('tasks:send-reminders')->assertSuccessful();

        $this->travelTo(Carbon::parse('2026-11-22 09:00:00'));
        $this->artisan('tasks:send-reminders')->assertSuccessful();

        Notification::assertSentToTimes($user, TaskDeadlineReminderNotification::class, 3);
        $this->assertDatabaseCount('reminders', 3);
        $this->assertDatabaseHas('reminders', [
            'task_id' => $task->id,
            'channel' => 'email',
        ]);
        $this->assertSame(
            3,
            $task->reminders()->whereNotNull('sent_at')->count(),
        );
    }

    public function test_it_skips_completed_tasks_and_non_reminder_days(): void
    {
        $this->travelTo(Carbon::parse('2026-11-19 09:00:00'));
        Notification::fake();

        Task::factory()->completed()->create([
            'deadline' => Carbon::parse('2026-11-22 12:00:00'),
        ]);
        Task::factory()->create([
            'deadline' => Carbon::parse('2026-11-21 12:00:00'),
        ]);

        $this->artisan('tasks:send-reminders')->assertSuccessful();

        Notification::assertNothingSent();
        $this->assertDatabaseCount('reminders', 0);
    }
}
