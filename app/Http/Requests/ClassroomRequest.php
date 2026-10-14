<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClassroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('classrooms', 'name')->ignore($this->route('classroom'))],
            'grade_level' => ['required', 'integer', Rule::in([10, 11, 12])],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama kelas',
            'grade_level' => 'tingkat',
            'teacher_id' => 'wali kelas',
        ];
    }
}