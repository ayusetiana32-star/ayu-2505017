<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskDeadlineReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Task $task,
        public int $daysRemaining,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengingat deadline: '.$this->task->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.task-deadline-reminder',
        );
    }
}
