<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ArticleRequest extends FormRequest
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
        return [
            'title' => 'required|string|min:10|max:255|unique:articles,title,' . ($this->article->id ?? 'NULL'),
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string|min:100',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|max:10240|mimes:jpeg,png,jpg,webp', // 10MB, will be auto-compressed
            'tags' => 'array|max:5',
            'tags.*' => 'exists:tags,id',
            'scheduled_at' => 'nullable|date|after:now',
            'action' => 'nullable|in:draft,publish',
        ];
    }

    /**
     * Get validated data with additional computed fields.
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);
        $user = $this->user();
        $action = $this->input('action', 'draft');

        // Determine status based on action and scheduled_at
        if ($action === 'publish') {
            if (!empty($validated['scheduled_at'])) {
                // Scheduled publication - set status to 'scheduled'
                $validated['status'] = 'scheduled';
                // Convert to Carbon datetime if string, treating input as WIB (Asia/Jakarta)
                if (is_string($validated['scheduled_at'])) {
                    $validated['scheduled_at'] = \Carbon\Carbon::parse($validated['scheduled_at'], 'Asia/Jakarta');
                }
            } else {
                // Immediate publication
                if ($user && ($user->isGuru() || $user->isAdmin())) {
                    // Admin/Guru can publish directly
                    $validated['status'] = 'published';
                    $validated['published_at'] = now();
                } else {
                    // Siswa needs review
                    $validated['status'] = 'pending';
                }
            }
        } else {
            $validated['status'] = 'draft';
        }

        // Remove action from validated data
        unset($validated['action']);

        return $validated;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul artikel harus diisi.',
            'title.min' => 'Judul artikel minimal 10 karakter.',
            'title.max' => 'Judul artikel maksimal 255 karakter.',
            'title.unique' => 'Judul artikel sudah digunakan.',
            'category_id.required' => 'Kategori harus dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'content.required' => 'Isi artikel harus diisi.',
            'content.min' => 'Isi artikel minimal 300 karakter.',
            'featured_image.image' => 'File harus berupa gambar.',
            'featured_image.max' => 'Ukuran gambar maksimal 2MB.',
            'tags.max' => 'Maksimal 5 tag per artikel.',
            'scheduled_at.after' => 'Waktu jadwal harus lebih besar dari sekarang.',
        ];
    }
}
