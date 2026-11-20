<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'title',
        'description',
        'deadline',
        'priority',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function getTimeRemainingAttribute(): string
    {
        $deadline = $this->deadline->copy()->locale('id');
        $difference = $deadline->diffForHumans(now(), true);

        return $deadline->isPast()
            ? "Terlambat {$difference}"
            : "{$difference} lagi";
    }

    public function getDeadlineBadgeClassAttribute(): string
    {
        if ($this->deadline->isPast() || $this->deadline->isToday()) {
            return 'bg-red-100 text-red-800';
        }

        if (now()->diffInHours($this->deadline, false) <= 72) {
            return 'bg-amber-100 text-amber-800';
        }

        return 'bg-green-100 text-green-800';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search === null || trim($search) === '') {
            return $query;
        }

        return $query->where('title', 'like', '%'.$search.'%');
    }

    public function scopeForCourse(Builder $query, ?string $courseId): Builder
    {
        return $query->when($courseId, fn (Builder $query) => $query->where('course_id', $courseId));
    }

    public function scopeWithStatus(Builder $query, ?string $status): Builder
    {
        return $query->when($status, fn (Builder $query) => $query->where('status', $status));
    }

    public function scopeWithPriority(Builder $query, ?string $priority): Builder
    {
        return $query->when($priority, fn (Builder $query) => $query->where('priority', $priority));
    }

    public function scopeDeadlineOrder(Builder $query, string $direction = 'asc'): Builder
    {
        $direction = in_array($direction, ['asc', 'desc'], true) ? $direction : 'asc';

        return $query->orderBy('deadline', $direction);
    }
}
