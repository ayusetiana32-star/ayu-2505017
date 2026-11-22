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
        return ['mail'];
    }

    public function toMail(User $notifiable): TaskDeadlineReminderMail
    {
        return (new TaskDeadlineReminderMail($this->task, $this->daysRemaining))
            ->to($notifiable->email);
    }
}
