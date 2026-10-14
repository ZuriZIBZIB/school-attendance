<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $student = $this->route('student');

        return [
            'nis' => ['required', 'string', 'max:20', Rule::unique('students', 'nis')->ignore($student)],
            'full_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'is_active' => ['boolean'],
            'email' => [
                Rule::requiredIf(fn () => (bool) $student?->user_id),
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($student?->user_id),
            ],
            'password' => [
                Rule::requiredIf(fn () => filled($this->email) && ! $student?->user_id),
                'nullable',
                'string',
                'min:8',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'nis' => 'NIS',
            'full_name' => 'nama lengkap',
            'gender' => 'jenis kelamin',
            'classroom_id' => 'kelas',
            'email' => 'email akun',
            'password' => 'password',
        ];
    }
}