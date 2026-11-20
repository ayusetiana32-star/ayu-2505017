<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Course::class);

        return view('courses.index', [
            'courses' => $request->user()
                ->courses()
                ->withCount('tasks')
                ->orderBy('name')
                ->paginate(9),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Course::class);

        return view('courses.create', ['course' => null]);
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $request->user()->courses()->create($request->validated());

        return to_route('courses.index')->with('status', 'Mata kuliah berhasil ditambahkan.');
    }

    public function edit(Course $course): View
    {
        Gate::authorize('update', $course);

        return view('courses.edit', compact('course'));
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $course->update($request->validated());

        return to_route('courses.index')->with('status', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        Gate::authorize('delete', $course);
        $course->delete();

        return to_route('courses.index')->with('status', 'Mata kuliah berhasil dihapus.');
    }
}
