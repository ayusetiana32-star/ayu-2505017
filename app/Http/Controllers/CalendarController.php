<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(): View
    {
        return view('calendar.index');
    }

    public function events(Request $request): JsonResponse
    {
        $events = $request->user()
            ->tasks()
            ->with('course:id,name,color')
            ->orderBy('deadline')
            ->get(['id', 'course_id', 'title', 'deadline'])
            ->map(fn (Task $task): array => [
                'id' => (string) $task->id,
                'title' => $task->title,
                'start' => $task->deadline->toIso8601String(),
                'url' => route('tasks.show', $task),
                'backgroundColor' => $task->course->color,
                'borderColor' => $task->course->color,
                'extendedProps' => [
                    'course' => $task->course->name,
                ],
            ]);

        return response()->json($events);
    }
}
