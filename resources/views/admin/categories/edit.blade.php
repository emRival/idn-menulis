@extends('layouts.app')

@section('title', 'Edit Kategori - Admin IDN Menulis')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-4">
        <a href="{{ route('admin.categories.index') }}" class="text-blue-600 hover:underline">← Kembali ke Daftar</a>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Kategori</h1>
        <p class="text-gray-600 mt-1">Ubah informasi kategori</p>
    </div>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow p-6 space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">Icon (Emoji)</label>
                    <input type="text" name="icon" id="icon" value="{{ old('icon', $category->icon ?? '📁') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('icon')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Warna *</label>
                    <input type="color" name="color" id="color" value="{{ old('color', $category->color ?? '#3B82F6') }}"
                           class="w-full h-12 border border-gray-300 rounded-lg cursor-pointer">
                    @error('color')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="order_position" class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="order_position" id="order_position" value="{{ old('order_position', $category->order_position ?? 0) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('order_position')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                       @checked(old('is_active', $category->is_active))>
                <label for="is_active" class="text-sm font-medium text-gray-700">Kategori Aktif</label>
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.categories.index') }}" class="px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
