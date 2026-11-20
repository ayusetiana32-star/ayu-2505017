<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('course')) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $course = $this->route('course');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('courses', 'code')
                    ->where('user_id', $this->user()->id)
                    ->ignore($course->id),
            ],
            'lecturer' => ['required', 'string', 'max:255'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }
}
