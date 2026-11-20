<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_manage_their_own_courses(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherCourse = Course::factory()->for($otherUser)->create();

        $this->actingAs($user)
            ->get(route('courses.index'))
            ->assertOk()
            ->assertDontSee($otherCourse->name);

        $this->get(route('courses.create'))->assertOk();

        $this->post(route('courses.store'), [
            'name' => 'Pemrograman Web',
            'code' => 'IF301',
            'lecturer' => 'Dr. Sari',
            'color' => '#2563eb',
        ])->assertRedirect(route('courses.index'));

        $course = $user->courses()->where('code', 'IF301')->firstOrFail();
        $this->assertSame($user->id, $course->user_id);

        $this->get(route('courses.edit', $course))->assertOk();

        $this->put(route('courses.update', $course), [
            'name' => 'Pemrograman Web Lanjut',
            'code' => 'IF302',
            'lecturer' => 'Dr. Sari',
            'color' => '#16a34a',
        ])->assertRedirect(route('courses.index'));

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Pemrograman Web Lanjut',
        ]);

        $this->delete(route('courses.destroy', $course))
            ->assertRedirect(route('courses.index'));

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_user_cannot_access_or_change_another_users_course(): void
    {
        $owner = User::factory()->create();
        $course = Course::factory()->for($owner)->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('courses.edit', $course))
            ->assertForbidden();

        $this->put(route('courses.update', $course), [
            'name' => 'Percobaan akses',
            'code' => $course->code,
            'lecturer' => $course->lecturer,
            'color' => $course->color,
        ])->assertForbidden();

        $this->delete(route('courses.destroy', $course))->assertForbidden();

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'user_id' => $owner->id,
        ]);
    }

    public function test_course_form_rejects_invalid_color_and_duplicate_user_code(): void
    {
        $user = User::factory()->create();
        Course::factory()->for($user)->create(['code' => 'IF101']);

        $this->actingAs($user)
            ->from(route('courses.create'))
            ->post(route('courses.store'), [
                'name' => 'Basis Data',
                'code' => 'IF101',
                'lecturer' => 'Dr. Budi',
                'color' => 'not-a-color',
            ])
            ->assertSessionHasErrors(['code', 'color']);
    }
}
