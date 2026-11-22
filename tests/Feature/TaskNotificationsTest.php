<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_reminder_command_creates_a_database_notification_shown_in_the_navbar(): void
    {
        $this->travelTo(Carbon::parse('2026-11-19 09:00:00'));
        $task = Task::factory()->create([
            'deadline' => Carbon::parse('2026-11-22 12:00:00'),
        ]);
        $user = $task->user;
        $user->forceFill(['email_verified_at' => now()])->save();

        $this->artisan('tasks:send-reminders')->assertSuccessful();

        $notification = $user->notifications()->firstOrFail();

        $this->assertSame('Pengingat deadline tugas', $notification->data['title']);
        $this->assertSame("Deadline {$task->title} tinggal 3 hari lagi.", $notification->data['message']);
        $this->assertNull($notification->read_at);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Notifikasi')
            ->assertSee('aria-label="Notifikasi, 1 belum dibaca"', false)
            ->assertSee('Tandai sudah dibaca')
            ->assertSee($task->title);

        $this->actingAs($user)
            ->patch(route('notifications.read', $notification->id))
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->read_at);
        $this->get(route('dashboard'))
            ->assertSee('aria-label="Notifikasi, 0 belum dibaca"', false)
            ->assertDontSee('Tandai sudah dibaca');
    }

    public function test_a_user_cannot_mark_another_users_notification_as_read(): void
    {
        $task = Task::factory()->create();
        $owner = $task->user;
        $notificationId = $owner->notifications()->create([
            'id' => (string) str()->uuid(),
            'type' => 'task-reminder',
            'data' => ['title' => 'Pengingat deadline tugas'],
        ])->id;

        $this->actingAs(User::factory()->create())
            ->patch(route('notifications.read', $notificationId))
            ->assertNotFound();

        $this->assertNull($owner->notifications()->findOrFail($notificationId)->read_at);
    }
}
