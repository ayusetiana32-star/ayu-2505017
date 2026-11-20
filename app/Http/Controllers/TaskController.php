<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Task::class);

        return view('tasks.index', [
            'tasks' => $request->user()
                ->tasks()
                ->with('course')
                ->orderBy('deadline')
                ->paginate(12),
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
