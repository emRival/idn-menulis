<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? 'NULL';

        return [
            'username' => 'required|alpha_dash|min:4|max:50|unique:users,username,' . $userId,
            'email' => 'required|email|unique:users,email,' . $userId,
            'full_name' => 'required|string|min:3|max:100',
            'school_name' => 'required|string|max:150',
            'class' => 'required_if:role,siswa|max:50',
            'role' => 'required|in:admin,guru,siswa',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Username harus diisi.',
            'username.alpha_dash' => 'Username hanya boleh mengandung huruf, angka, dan garis bawah.',
            'username.min' => 'Username minimal 4 karakter.',
            'username.max' => 'Username maksimal 50 karakter.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'full_name.required' => 'Nama lengkap harus diisi.',
            'full_name.min' => 'Nama lengkap minimal 3 karakter.',
            'school_name.required' => 'Nama sekolah harus diisi.',
            'class.required_if' => 'Kelas harus diisi untuk siswa.',
        ];
    }
}
