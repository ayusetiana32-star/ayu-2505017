<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Task::class);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'course_id' => [
                'nullable',
                'integer',
                Rule::exists('courses', 'id')->where('user_id', $request->user()->id),
            ],
            'status' => ['nullable', Rule::in(['belum', 'proses', 'selesai'])],
            'priority' => ['nullable', Rule::in(['rendah', 'sedang', 'tinggi'])],
            'deadline_order' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);

        return view('tasks.index', [
            'tasks' => $request->user()
                ->tasks()
                ->with('course')
                ->search($filters['search'] ?? null)
                ->forCourse(isset($filters['course_id']) ? (string) $filters['course_id'] : null)
                ->withStatus($filters['status'] ?? null)
                ->withPriority($filters['priority'] ?? null)
                ->deadlineOrder($filters['deadline_order'] ?? 'asc')
                ->paginate(12)
                ->withQueryString(),
            'courses' => $request->user()->courses()->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Task::class);

        return view('tasks.create', [
            'task' => null,
            'courses' => $request->user()->courses()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['completed_at'] = $data['status'] === 'selesai' ? now() : null;

        $request->user()->tasks()->create($data);

        return to_route('tasks.index')->with('status', 'Tugas berhasil ditambahkan.');
    }

    public function edit(Request $request, Task $task): View
    {
        Gate::authorize('update', $task);

        return view('tasks.edit', [
            'task' => $task->load('course'),
            'courses' => $request->user()->courses()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $data = $request->validated();
        $data['completed_at'] = $data['status'] === 'selesai'
            ? ($task->completed_at ?? now())
            : null;

        $task->update($data);

        return to_route('tasks.index')->with('status', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        Gate::authorize('delete', $task);
        $task->delete();

        return to_route('tasks.index')->with('status', 'Tugas berhasil dihapus.');
    }

    public function toggleCompletion(Task $task): RedirectResponse
    {
        Gate::authorize('update', $task);

        $task->status = $task->status === 'selesai' ? 'belum' : 'selesai';
        $task->completed_at = $task->status === 'selesai' ? now() : null;
        $task->save();

        return to_route('tasks.index')->with(
            'status',
            $task->status === 'selesai' ? 'Tugas ditandai selesai.' : 'Tugas ditandai belum selesai.',
        );
    }
}
