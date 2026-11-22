<p>Halo {{ $task->user->name }},</p>

@if ($daysRemaining === 0)
    <p>Deadline tugas berikut jatuh hari ini:</p>
@else
    <p>Deadline tugas berikut tinggal {{ $daysRemaining }} hari lagi:</p>
@endif

<p><strong>{{ $task->title }}</strong></p>
<p>Deadline: {{ $task->deadline->format('d-m-Y H:i') }}</p>
<p>Segera selesaikan tugas sebelum deadline.</p>
