<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SecureFileUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $maxSize = config('security.upload.max_size_kb', 5120);
        $allowedMimes = implode(',', config('security.upload.allowed_extensions', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx']));

        return [
            'file' => [
                'nullable',
                'file',
                "max:{$maxSize}",
                "mimes:{$allowedMimes}",
            ],
            'image' => [
                'nullable',
                'image',
                "max:{$maxSize}",
                'mimes:jpg,jpeg,png,gif,webp',
                'dimensions:max_width=4096,max_height=4096',
            ],
            'avatar' => [
                'nullable',
                'image',
                'max:10240', // 10MB for avatars, will be auto-compressed
                'mimes:jpg,jpeg,png,gif,webp',
                'dimensions:min_width=100,min_height=100,max_width=2048,max_height=2048',
            ],
            'cover_image' => [
                'nullable',
                'image',
                'max:10240', // 10MB for cover, will be auto-compressed
                'mimes:jpg,jpeg,png,gif,webp',
                'dimensions:min_width=800,max_width=4096,max_height=4096',
            ],
            'featured_image' => [
                'nullable',
                'image',
                'max:10240', // 10MB for featured, will be auto-compressed
                'mimes:jpg,jpeg,png,gif,webp',
                'dimensions:min_width=600,max_width=4096,max_height=4096',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.max' => 'Ukuran file terlalu besar. Maksimal :max KB.',
            'file.mimes' => 'Tipe file tidak diizinkan.',
            'image.max' => 'Ukuran gambar terlalu besar. Maksimal :max KB.',
            'image.mimes' => 'Format gambar tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.',
            'image.dimensions' => 'Dimensi gambar tidak valid.',
            'avatar.max' => 'Ukuran avatar terlalu besar. Maksimal 10MB (akan dikompres otomatis).',
            'avatar.dimensions' => 'Dimensi avatar harus minimal 100x100 piksel.',
            'cover_image.max' => 'Ukuran cover terlalu besar. Maksimal 10MB (akan dikompres otomatis).',
            'cover_image.dimensions' => 'Lebar cover minimal 800 piksel.',
            'featured_image.max' => 'Ukuran featured image terlalu besar. Maksimal 10MB (akan dikompres otomatis).',
            'featured_image.dimensions' => 'Lebar featured image minimal 600 piksel.',
        ];
    }
}
