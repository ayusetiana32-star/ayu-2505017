<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskFilteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_list_can_combine_search_course_status_and_priority_filters(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->for($user)->create();
        $otherCourse = Course::factory()->for($user)->create();

        $match = $this->createTask($user, $course, 'Compiler report', 'proses', 'tinggi');
        $this->createTask($user, $course, 'Compiler notes', 'belum', 'tinggi');
        $this->createTask($user, $otherCourse, 'Compiler lab', 'proses', 'tinggi');

        $this->actingAs($user)
            ->get(route('tasks.index', [
                'search' => 'Compiler report',
                'course_id' => $course->id,
                'status' => 'proses',
                'priority' => 'tinggi',
                'deadline_order' => 'desc',
            ]))
            ->assertOk()
            ->assertSee($match->title)
            ->assertDontSee('Compiler notes')
            ->assertDontSee('Compiler lab');
    }

    public function test_deadline_order_can_be_selected_in_both_directions(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->for($user)->create();
        $this->createTask($user, $course, 'Deadline awal', 'belum', 'sedang', now()->addDay());
        $this->createTask($user, $course, 'Deadline akhir', 'belum', 'sedang', now()->addDays(4));

        $this->actingAs($user)
            ->get(route('tasks.index', ['deadline_order' => 'desc']))
            ->assertOk()
            ->assertSeeInOrder(['Deadline akhir', 'Deadline awal']);

        $this->get(route('tasks.index', ['deadline_order' => 'asc']))
            ->assertOk()
            ->assertSeeInOrder(['Deadline awal', 'Deadline akhir']);
    }

    public function test_pagination_preserves_active_filter_query_parameters(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->for($user)->create();

        foreach (range(1, 13) as $number) {
            $this->createTask(
                $user,
                $course,
                sprintf('Pagination task %02d', $number),
                'belum',
                'sedang',
                now()->addDays($number),
            );
        }

        $this->actingAs($user)
            ->get(route('tasks.index', [
                'search' => 'Pagination task',
                'course_id' => $course->id,
                'status' => 'belum',
                'priority' => 'sedang',
                'deadline_order' => 'desc',
                'page' => 2,
            ]))
            ->assertOk()
            ->assertViewHas('tasks', fn ($tasks) => $tasks->currentPage() === 2 && $tasks->total() === 13)
            ->assertSee('search=Pagination%20task', false)
            ->assertSee('course_id='.$course->id, false)
            ->assertSee('status=belum', false)
            ->assertSee('priority=sedang', false)
            ->assertSee('deadline_order=desc', false);
    }

    private function createTask(
        User $user,
        Course $course,
        string $title,
        string $status,
        string $priority,
        ?Carbon $deadline = null,
    ): Task {
        return Task::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'title' => $title,
            'status' => $status,
            'priority' => $priority,
            'deadline' => $deadline ?? now()->addDay(),
        ]);
    }
}
