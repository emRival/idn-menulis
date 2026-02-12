@extends('layouts.app')

@section('title', 'Edit Tag - Admin IDN Menulis')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-4">
        <a href="{{ route('admin.tags.index') }}" class="text-blue-600 hover:underline">← Kembali ke Daftar</a>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Tag</h1>
        <p class="text-gray-600 mt-1">Ubah informasi tag</p>
    </div>

    <form action="{{ route('admin.tags.update', $tag) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow p-6 space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Tag *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $tag->name) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.tags.index') }}" class="px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
