<?php

namespace App\Notifications;

use App\Mail\TaskDeadlineReminderMail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Notifications\Notification;

class TaskDeadlineReminderNotification extends Notification
{
    public function __construct(
        public Task $task,
        public int $daysRemaining,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): TaskDeadlineReminderMail
    {
        return (new TaskDeadlineReminderMail($this->task, $this->daysRemaining))
            ->to($notifiable->email);
    }

    /**
     * @return array<string, int|string>
     */
    public function toDatabase(User $notifiable): array
    {
        return [
            'title' => 'Pengingat deadline tugas',
            'message' => $this->daysRemaining === 0
                ? "Deadline {$this->task->title} jatuh hari ini."
                : "Deadline {$this->task->title} tinggal {$this->daysRemaining} hari lagi.",
            'task_id' => $this->task->id,
            'days_remaining' => $this->daysRemaining,
        ];
    }
}
