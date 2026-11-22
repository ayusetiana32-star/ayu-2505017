<?php

namespace App\Console\Commands;

use App\Models\Reminder;
use App\Models\Task;
use App\Notifications\TaskDeadlineReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendTaskReminders extends Command
{
    protected $signature = 'tasks:send-reminders';

    protected $description = 'Send email reminders for unfinished tasks approaching their deadlines';

    public function handle(): int
    {
        $today = now()->startOfDay();
        $reminderDays = [3, 1, 0];
        $sentCount = 0;

        Task::query()
            ->with('user')
            ->where('status', '!=', 'selesai')
            ->whereDate('deadline', '>=', $today->toDateString())
            ->whereDate('deadline', '<=', $today->copy()->addDays(3)->toDateString())
            ->orderBy('id')
            ->chunkById(100, function ($tasks) use ($today, $reminderDays, &$sentCount): void {
                foreach ($tasks as $task) {
                    $daysUntilDeadline = (int) $today->diffInDays($task->deadline->copy()->startOfDay());

                    if (! in_array($daysUntilDeadline, $reminderDays, true)) {
                        continue;
                    }

                    $reminderAt = $task->deadline->copy()->startOfDay()->subDays($daysUntilDeadline);
                    $reminder = Reminder::query()->firstOrCreate([
                        'task_id' => $task->id,
                        'remind_at' => $reminderAt,
                        'channel' => 'email',
                    ]);

                    $sent = DB::transaction(function () use ($reminder, $task, $daysUntilDeadline): bool {
                        $lockedReminder = Reminder::query()
                            ->whereKey($reminder->id)
                            ->lockForUpdate()
                            ->firstOrFail();

                        if ($lockedReminder->sent_at !== null) {
                            return false;
                        }

                        $task->user->notify(new TaskDeadlineReminderNotification($task, $daysUntilDeadline));

                        $lockedReminder->forceFill(['sent_at' => now()])->save();

                        return true;
                    });

                    if ($sent) {
                        $sentCount++;
                    }
                }
            });

        $this->info("Sent {$sentCount} task reminder(s).");

        return self::SUCCESS;
    }
}
