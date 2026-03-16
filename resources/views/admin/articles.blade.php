@extends('layouts.app')

@section('title', 'Kelola Artikel - Admin IDN Menulis')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kelola Artikel</h1>
            <p class="text-gray-600 mt-1">Semua artikel dari semua pengguna</p>
        </div>
        <div class="flex gap-3 text-sm">
            <span class="px-3 py-1.5 rounded-full bg-teal-100 text-teal-700 font-medium">{{ $stats['published'] }} Published</span>
            <span class="px-3 py-1.5 rounded-full bg-amber-100 text-amber-700 font-medium">{{ $stats['pending'] }} Pending</span>
            <span class="px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 font-medium">{{ $stats['draft'] }} Draft</span>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('admin.articles') }}" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari judul atau penulis..."
                   class="flex-1 min-w-[200px] px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">

            <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500">
                <option value="all">Semua Status</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="revision" {{ request('status') === 'revision' ? 'selected' : '' }}>Revision</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>

            <select name="category" class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-5 py-2 bg-teal-600 text-white rounded-lg text-sm font-medium hover:bg-teal-700 transition-colors">
                Filter
            </button>

            @if(request()->hasAny(['search', 'status', 'category']))
                <a href="{{ route('admin.articles') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 text-sm">Reset</a>
            @endif
        </form>
    </div>

    <!-- Articles Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Artikel</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Penulis</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Kategori</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Views</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($articles as $article)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('articles.show', $article) }}" class="text-sm font-medium text-gray-900 hover:text-teal-600 line-clamp-1" target="_blank">
                                    {{ Str::limit($article->title, 50) }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $article->user->full_name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $article->category->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'published' => 'bg-green-100 text-green-700',
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'draft' => 'bg-gray-100 text-gray-700',
                                        'revision' => 'bg-blue-100 text-blue-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$article->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($article->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ number_format($article->views_count) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $article->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('articles.edit', $article) }}"
                                       class="inline-flex items-center px-3 py-1.5 bg-teal-50 text-teal-700 rounded-lg text-xs font-medium hover:bg-teal-100 transition-colors">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Hapus artikel ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-xs font-medium hover:bg-red-100 transition-colors">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Tidak ada artikel ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($articles->hasPages())
        <div class="mt-6">
            {{ $articles->links() }}
        </div>
    @endif
</div>
@endsection
