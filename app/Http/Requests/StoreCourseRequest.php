<?php

namespace App\Http\Requests;

use App\Models\Course;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Course::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('courses', 'code')->where('user_id', $this->user()->id),
            ],
            'lecturer' => ['required', 'string', 'max:255'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }
}
