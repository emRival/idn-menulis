@extends('layouts.app')

@section('title', 'Kelola Tag - Admin IDN Menulis')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kelola Tag</h1>
            <p class="text-gray-600 mt-1">Kelola tag artikel</p>
        </div>
        <a href="{{ route('admin.tags.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
            + Tambah Tag
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form action="{{ route('admin.tags.index') }}" method="GET" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                   placeholder="Cari tag...">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Cari
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Tag</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Slug</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Penggunaan</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($tags as $tag)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $tag->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $tag->slug }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $tag->usage_count ?? $tag->articles()->count() }} artikel
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.tags.edit', $tag) }}" class="text-blue-600 hover:underline text-sm">Edit</a>
                                <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Hapus tag ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            Belum ada tag.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tags->hasPages())
        <div class="mt-6">
            {{ $tags->links() }}
        </div>
    @endif
</div>
@endsection
