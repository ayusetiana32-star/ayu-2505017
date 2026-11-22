<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_edit_toggle_and_delete_their_tasks(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->for($user)->create();
        $this->actingAs($user);

        $this->get(route('tasks.create'))->assertOk();

        $this->post(route('tasks.store'), [
            'course_id' => $course->id,
            'title' => 'Tugas Algoritma',
            'description' => 'Bandingkan dua algoritma sorting.',
            'deadline' => now()->addDays(3)->format('Y-m-d\TH:i'),
            'priority' => 'tinggi',
            'status' => 'proses',
        ])->assertRedirect(route('tasks.index'));

        $task = $user->tasks()->where('title', 'Tugas Algoritma')->firstOrFail();
        $this->assertSame($course->id, $task->course_id);
        $this->assertNull($task->completed_at);
        $this->get(route('tasks.show', $task))->assertOk()->assertSee('Tugas Algoritma');
        $this->get(route('tasks.edit', $task))->assertOk();

        $this->put(route('tasks.update', $task), [
            'course_id' => $course->id,
            'title' => 'Tugas Algoritma Lanjut',
            'description' => 'Analisis kompleksitas algoritma sorting.',
            'deadline' => now()->addDays(5)->format('Y-m-d\TH:i'),
            'priority' => 'sedang',
            'status' => 'selesai',
        ])->assertRedirect(route('tasks.index'));

        $task->refresh();
        $this->assertSame('selesai', $task->status);
        $this->assertNotNull($task->completed_at);

        $this->patch(route('tasks.toggle-completion', $task))
            ->assertRedirect(route('tasks.index'));

        $this->assertSame('belum', $task->refresh()->status);
        $this->assertNull($task->completed_at);

        $this->delete(route('tasks.destroy', $task))
            ->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_only_sees_their_tasks_and_owned_courses_in_forms(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $course = Course::factory()->for($user)->create(['name' => 'Basis Data Saya']);
        $otherCourse = Course::factory()->for($otherUser)->create(['name' => 'Kelas Privat']);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'title' => 'Tugas Milik Saya',
        ]);
        $otherTask = Task::factory()->create([
            'user_id' => $otherUser->id,
            'course_id' => $otherCourse->id,
            'title' => 'Tugas Rahasia',
        ]);

        $this->actingAs($user)
            ->get(route('tasks.index'))
            ->assertOk()
            ->assertSee('Tugas Milik Saya')
            ->assertDontSee('Tugas Rahasia');

        $this->get(route('tasks.create'))
            ->assertOk()
            ->assertSee('Basis Data Saya')
            ->assertDontSee('Kelas Privat');

        $this->get(route('tasks.show', $otherTask))->assertForbidden();
        $this->get(route('tasks.edit', $otherTask))->assertForbidden();
        $this->put(route('tasks.update', $otherTask), [
            'course_id' => $course->id,
            'title' => 'Akses Ilegal',
            'description' => 'Tidak boleh diubah.',
            'deadline' => now()->addDay()->format('Y-m-d\TH:i'),
            'priority' => 'rendah',
            'status' => 'belum',
        ])->assertForbidden();
        $this->patch(route('tasks.toggle-completion', $otherTask))->assertForbidden();
        $this->delete(route('tasks.destroy', $otherTask))->assertForbidden();

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'user_id' => $user->id]);
    }

    public function test_task_cannot_be_assigned_to_another_users_course(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherCourse = Course::factory()->for($otherUser)->create();

        $this->actingAs($user)
            ->from(route('tasks.create'))
            ->post(route('tasks.store'), [
                'course_id' => $otherCourse->id,
                'title' => 'Tugas tidak valid',
                'description' => 'Mata kuliah ini bukan milik user.',
                'deadline' => now()->addDay()->format('Y-m-d\TH:i'),
                'priority' => 'sedang',
                'status' => 'belum',
            ])
            ->assertSessionHasErrors('course_id');

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_task_index_query_count_does_not_grow_with_each_task(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->for($user)->create();
        Task::factory()->for($course)->create();

        $oneTaskQueryCount = $this->taskIndexQueryCount($user);

        Task::factory()->count(5)->for($course)->create();

        $manyTasksQueryCount = $this->taskIndexQueryCount($user);

        $this->assertSame($oneTaskQueryCount, $manyTasksQueryCount);
    }

    private function taskIndexQueryCount(User $user): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->actingAs($user)->get(route('tasks.index'))->assertOk();

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $queryCount;
    }
}
