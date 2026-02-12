@extends('layouts.app')

@section('title', 'Persetujuan Artikel - IDN Menulis')

@section('styles')
<style>
    .stats-card {
        transition: all 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-2px);
    }
    .article-row {
        transition: all 0.2s ease;
    }
    .article-row:hover {
        background-color: rgb(249 250 251);
    }
    .checkbox-select {
        accent-color: #3b82f6;
    }
    .bulk-actions-bar {
        transition: all 0.3s ease;
    }
    .modal-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }
    .cover-thumbnail {
        width: 80px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
    }
    .status-badge {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 9999px;
        font-weight: 600;
        text-transform: uppercase;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50" x-data="approvalManager()">
    <!-- Sticky Header -->
    <div class="sticky top-0 z-40 bg-white border-b shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Breadcrumb & Title -->
                <div>
                    <nav class="flex items-center text-sm text-gray-500 mb-1">
                        <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="text-gray-700 font-medium">Persetujuan Artikel</span>
                    </nav>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-900 flex items-center gap-3">
                        Persetujuan Artikel
                        <span class="text-sm font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                            {{ $stats['pending'] }} Menunggu
                        </span>
                    </h1>
                </div>

                <!-- Status Badges -->
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('approvals.pending', ['status' => 'pending']) }}"
                       class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ request('status', 'pending') == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600 hover:bg-yellow-50' }}">
                        <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span>
                        Pending <span class="ml-1.5 bg-yellow-200 text-yellow-700 px-1.5 rounded-full text-xs">{{ $stats['pending'] }}</span>
                    </a>
                    <a href="{{ route('approvals.pending', ['status' => 'revision']) }}"
                       class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ request('status') == 'revision' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600 hover:bg-blue-50' }}">
                        <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                        Revisi <span class="ml-1.5 bg-blue-200 text-blue-700 px-1.5 rounded-full text-xs">{{ $stats['revision'] }}</span>
                    </a>
                    <a href="{{ route('approvals.pending', ['status' => 'published']) }}"
                       class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ request('status') == 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600 hover:bg-green-50' }}">
                        <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                        Disetujui <span class="ml-1.5 bg-green-200 text-green-700 px-1.5 rounded-full text-xs">{{ $stats['approved'] }}</span>
                    </a>
                    <a href="{{ route('approvals.pending', ['status' => 'rejected']) }}"
                       class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ request('status') == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600 hover:bg-red-50' }}">
                        <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>
                        Ditolak <span class="ml-1.5 bg-red-200 text-red-700 px-1.5 rounded-full text-xs">{{ $stats['rejected'] }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Flash Messages -->
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-green-800 font-medium">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="stats-card bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-4 border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-600 text-sm font-medium">Pending</p>
                        <p class="text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="stats-card bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-600 text-sm font-medium">Revisi</p>
                        <p class="text-2xl font-bold text-blue-700">{{ $stats['revision'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="stats-card bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-600 text-sm font-medium">Disetujui</p>
                        <p class="text-2xl font-bold text-green-700">{{ $stats['approved'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="stats-card bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-600 text-sm font-medium">Ditolak</p>
                        <p class="text-2xl font-bold text-red-700">{{ $stats['rejected'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
            <form method="GET" action="{{ route('approvals.pending') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari judul atau penulis..."
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                    <!-- Category -->
                    <div>
                        <select name="category" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Class -->
                    <div>
                        <select name="class" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class }}" {{ request('class') == $class ? 'selected' : '' }}>{{ $class }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Date From -->
                    <div>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Dari Tanggal">
                    </div>
                    <!-- Sort -->
                    <div>
                        <select name="sort" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="oldest" {{ request('sort', 'oldest') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <input type="hidden" name="status" value="{{ request('status', 'pending') }}">
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('approvals.pending') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Reset
                        </a>
                    </div>
                    <p class="text-sm text-gray-500">
                        Menampilkan {{ $articles->count() }} dari {{ $articles->total() }} artikel
                    </p>
                </div>
            </form>
        </div>

        <!-- Bulk Actions Bar (shown when items selected) -->
        <div x-show="selectedIds.length > 0" x-cloak
             class="bulk-actions-bar fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-full shadow-xl z-50 flex items-center gap-4">
            <span class="font-medium"><span x-text="selectedIds.length"></span> artikel dipilih</span>
            <div class="h-6 w-px bg-gray-600"></div>
            <button @click="bulkApprove()" class="flex items-center gap-2 px-3 py-1.5 bg-green-600 rounded-full hover:bg-green-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Setujui
            </button>
            <button @click="showBulkRevisionModal = true" class="flex items-center gap-2 px-3 py-1.5 bg-blue-600 rounded-full hover:bg-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Revisi
            </button>
            <button @click="showBulkRejectModal = true" class="flex items-center gap-2 px-3 py-1.5 bg-red-600 rounded-full hover:bg-red-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Tolak
            </button>
            <button @click="selectedIds = []; selectAll = false" class="text-gray-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Articles Table -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()"
                                       class="checkbox-select rounded border-gray-300 w-4 h-4">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cover</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Judul Artikel</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Penulis</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Submit</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($articles as $article)
                            <tr class="article-row" data-id="{{ $article->id }}">
                                <td class="px-4 py-3">
                                    <input type="checkbox" :value="{{ $article->id }}" x-model="selectedIds"
                                           class="checkbox-select rounded border-gray-300 w-4 h-4">
                                </td>
                                <td class="px-4 py-3">
                                    @if($article->cover_image)
                                        <img src="{{ asset('storage/' . $article->cover_image) }}"
                                             alt="Cover" class="cover-thumbnail">
                                    @else
                                        <div class="w-20 h-12 bg-gray-100 rounded-md flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="max-w-xs">
                                        <p class="font-medium text-gray-900 truncate" title="{{ $article->title }}">
                                            {{ Str::limit($article->title, 45) }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate">{{ Str::limit(strip_tags($article->excerpt ?? $article->content), 60) }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $article->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($article->user->full_name ?? 'User') }}"
                                             alt="Avatar" class="w-8 h-8 rounded-full">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $article->user->full_name ?? '-' }}</p>
                                            <p class="text-xs text-gray-500">{{ $article->user->class ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ $article->category->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-600">{{ $article->created_at->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ $article->created_at->format('H:i') }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'pending_review' => 'bg-yellow-100 text-yellow-700',
                                            'revision' => 'bg-blue-100 text-blue-700',
                                            'published' => 'bg-green-100 text-green-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Pending',
                                            'pending_review' => 'Pending',
                                            'revision' => 'Revisi',
                                            'published' => 'Disetujui',
                                            'rejected' => 'Ditolak',
                                        ];
                                    @endphp
                                    <span class="status-badge {{ $statusClasses[$article->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $statusLabels[$article->status] ?? $article->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- Preview -->
                                        <button @click="previewArticle({{ $article->id }})"
                                                class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Preview">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        @if(in_array($article->status, ['pending', 'pending_review', 'revision']))
                                        <!-- Approve -->
                                        <button @click="approveArticle({{ $article->id }})"
                                                class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                                title="Setujui">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                        <!-- Revision -->
                                        <button @click="openRevisionModal({{ $article->id }}, '{{ addslashes($article->title) }}')"
                                                class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Minta Revisi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <!-- Reject -->
                                        <button @click="openRejectModal({{ $article->id }}, '{{ addslashes($article->title) }}')"
                                                class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Tolak">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                        @endif
                                        <!-- History -->
                                        <a href="{{ route('approvals.history', $article->id) }}"
                                           class="p-2 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                                           title="Riwayat">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-gray-500 font-medium">Tidak ada artikel</p>
                                        <p class="text-gray-400 text-sm">Belum ada artikel yang perlu direview saat ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($articles->hasPages())
        <div class="mt-6 flex items-center justify-between">
            <p class="text-sm text-gray-600">
                Menampilkan {{ $articles->firstItem() }} - {{ $articles->lastItem() }} dari {{ $articles->total() }} artikel
            </p>
            <div>
                {{ $articles->links() }}
            </div>
        </div>
        @endif
    </div>

    <!-- Preview Modal -->
    <div x-show="showPreviewModal" x-cloak @keydown.escape.window="showPreviewModal = false"
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
        <div @click.away="showPreviewModal = false"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Preview Artikel</h3>
                <button @click="showPreviewModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <template x-if="previewData">
                    <div>
                        <!-- Cover -->
                        <template x-if="previewData.cover_image">
                            <img :src="'/storage/' + previewData.cover_image" alt="Cover" class="w-full h-64 object-cover rounded-lg mb-6">
                        </template>
                        <!-- Title -->
                        <h1 class="text-2xl font-bold text-gray-900 mb-4" x-text="previewData.title"></h1>
                        <!-- Meta -->
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-6">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span x-text="previewData.user?.full_name"></span>
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <span x-text="previewData.category?.name"></span>
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span x-text="new Date(previewData.created_at).toLocaleDateString('id-ID')"></span>
                            </span>
                        </div>
                        <!-- Tags -->
                        <template x-if="previewData.tags && previewData.tags.length > 0">
                            <div class="flex flex-wrap gap-2 mb-6">
                                <template x-for="tag in previewData.tags" :key="tag.id">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs" x-text="tag.name"></span>
                                </template>
                            </div>
                        </template>
                        <!-- Content -->
                        <div class="prose max-w-none" x-html="previewData.content"></div>
                    </div>
                </template>
                <template x-if="previewLoading">
                    <div class="flex items-center justify-center py-12">
                        <svg class="animate-spin w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </template>
            </div>
            <!-- Footer -->
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
                <button @click="showPreviewModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Tutup
                </button>
                <template x-if="previewData && ['pending', 'pending_review', 'revision'].includes(previewData.status)">
                    <div class="flex gap-2">
                        <button @click="approveFromPreview()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Setujui
                        </button>
                        <button @click="revisionFromPreview()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Revisi
                        </button>
                        <button @click="rejectFromPreview()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Tolak
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Revision Modal -->
    <div x-show="showRevisionModal" x-cloak @keydown.escape.window="showRevisionModal = false"
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
        <div @click.away="showRevisionModal = false"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Minta Revisi Artikel</h3>
                <p class="text-sm text-gray-500 mt-1" x-text="'Artikel: ' + revisionTitle"></p>
            </div>
            <form @submit.prevent="submitRevision()">
                <div class="p-6 space-y-4">
                    <!-- Template Buttons -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Template Catatan:</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="revisionNotes = 'Mohon perbaiki tata bahasa dan ejaan.'"
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full">Tata Bahasa</button>
                            <button type="button" @click="revisionNotes = 'Tambahkan sumber referensi yang jelas.'"
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full">Referensi</button>
                            <button type="button" @click="revisionNotes = 'Perjelas paragraf pembuka dan penutup.'"
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full">Struktur</button>
                            <button type="button" @click="revisionNotes = 'Gambar cover perlu diganti dengan yang lebih relevan.'"
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full">Cover</button>
                        </div>
                    </div>
                    <!-- Notes Textarea -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Revisi <span class="text-red-500">*</span></label>
                        <textarea x-model="revisionNotes" rows="4" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Jelaskan hal-hal yang perlu diperbaiki oleh penulis..."></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" @click="showRevisionModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="submit" :disabled="loading" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                        <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Kirim Permintaan Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div x-show="showRejectModal" x-cloak @keydown.escape.window="showRejectModal = false"
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
        <div @click.away="showRejectModal = false"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Tolak Artikel</h3>
                <p class="text-sm text-gray-500 mt-1" x-text="'Artikel: ' + rejectTitle"></p>
            </div>
            <form @submit.prevent="submitReject()">
                <div class="p-6 space-y-4">
                    <!-- Template Buttons -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Template Alasan:</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="rejectReason = 'Artikel tidak sesuai dengan tema/topik yang diperbolehkan.'"
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full">Tidak Sesuai Tema</button>
                            <button type="button" @click="rejectReason = 'Artikel mengandung konten yang tidak pantas.'"
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full">Konten Tidak Pantas</button>
                            <button type="button" @click="rejectReason = 'Artikel terdeteksi sebagai plagiarisme.'"
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full">Plagiarisme</button>
                            <button type="button" @click="rejectReason = 'Kualitas penulisan tidak memenuhi standar minimum.'"
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full">Kualitas Rendah</button>
                        </div>
                    </div>
                    <!-- Reason Textarea -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                        <textarea x-model="rejectReason" rows="4" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                  placeholder="Jelaskan alasan penolakan artikel..."></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" @click="showRejectModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="submit" :disabled="loading" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center gap-2">
                        <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Tolak Artikel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Revision Modal -->
    <div x-show="showBulkRevisionModal" x-cloak @keydown.escape.window="showBulkRevisionModal = false"
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
        <div @click.away="showBulkRevisionModal = false"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Revisi Massal</h3>
                <p class="text-sm text-gray-500 mt-1"><span x-text="selectedIds.length"></span> artikel akan diminta revisi</p>
            </div>
            <form @submit.prevent="submitBulkRevision()">
                <div class="p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Revisi <span class="text-red-500">*</span></label>
                    <textarea x-model="bulkNotes" rows="4" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Catatan yang akan dikirim ke semua penulis..."></textarea>
                </div>
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" @click="showBulkRevisionModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="submit" :disabled="loading" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Kirim Permintaan Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Reject Modal -->
    <div x-show="showBulkRejectModal" x-cloak @keydown.escape.window="showBulkRejectModal = false"
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
        <div @click.away="showBulkRejectModal = false"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Tolak Massal</h3>
                <p class="text-sm text-gray-500 mt-1"><span x-text="selectedIds.length"></span> artikel akan ditolak</p>
            </div>
            <form @submit.prevent="submitBulkReject()">
                <div class="p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea x-model="bulkNotes" rows="4" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              placeholder="Alasan yang akan dikirim ke semua penulis..."></textarea>
                </div>
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" @click="showBulkRejectModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="submit" :disabled="loading" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Tolak Artikel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function approvalManager() {
    return {
        selectedIds: [],
        selectAll: false,
        loading: false,

        // Preview
        showPreviewModal: false,
        previewData: null,
        previewLoading: false,

        // Revision
        showRevisionModal: false,
        revisionId: null,
        revisionTitle: '',
        revisionNotes: '',

        // Reject
        showRejectModal: false,
        rejectId: null,
        rejectTitle: '',
        rejectReason: '',

        // Bulk modals
        showBulkRevisionModal: false,
        showBulkRejectModal: false,
        bulkNotes: '',

        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedIds = [...document.querySelectorAll('tbody tr[data-id]')].map(row => parseInt(row.dataset.id));
            } else {
                this.selectedIds = [];
            }
        },

        async previewArticle(id) {
            this.showPreviewModal = true;
            this.previewLoading = true;
            this.previewData = null;

            try {
                const response = await fetch(`/persetujuan/${id}/data`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                this.previewData = data.article;
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal memuat preview artikel');
            } finally {
                this.previewLoading = false;
            }
        },

        async approveArticle(id) {
            if (!confirm('Setujui artikel ini?')) return;

            this.loading = true;
            try {
                const response = await fetch(`/persetujuan/${id}/setujui`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({})
                });
                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal menyetujui artikel');
            } finally {
                this.loading = false;
            }
        },

        openRevisionModal(id, title) {
            this.revisionId = id;
            this.revisionTitle = title;
            this.revisionNotes = '';
            this.showRevisionModal = true;
        },

        async submitRevision() {
            this.loading = true;
            try {
                const response = await fetch(`/persetujuan/${this.revisionId}/revisi`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ revision_notes: this.revisionNotes })
                });
                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal mengirim permintaan revisi');
            } finally {
                this.loading = false;
            }
        },

        openRejectModal(id, title) {
            this.rejectId = id;
            this.rejectTitle = title;
            this.rejectReason = '';
            this.showRejectModal = true;
        },

        async submitReject() {
            this.loading = true;
            try {
                const response = await fetch(`/persetujuan/${this.rejectId}/tolak`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ rejection_reason: this.rejectReason })
                });
                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal menolak artikel');
            } finally {
                this.loading = false;
            }
        },

        async bulkApprove() {
            if (!confirm(`Setujui ${this.selectedIds.length} artikel ini?`)) return;

            this.loading = true;
            try {
                const response = await fetch('/persetujuan/bulk-action', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ action: 'approve', ids: this.selectedIds })
                });
                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal menyetujui artikel');
            } finally {
                this.loading = false;
            }
        },

        async submitBulkRevision() {
            this.loading = true;
            try {
                const response = await fetch('/persetujuan/bulk-action', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ action: 'revision', ids: this.selectedIds, notes: this.bulkNotes })
                });
                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal mengirim permintaan revisi');
            } finally {
                this.loading = false;
                this.showBulkRevisionModal = false;
            }
        },

        async submitBulkReject() {
            this.loading = true;
            try {
                const response = await fetch('/persetujuan/bulk-action', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ action: 'reject', ids: this.selectedIds, notes: this.bulkNotes })
                });
                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal menolak artikel');
            } finally {
                this.loading = false;
                this.showBulkRejectModal = false;
            }
        },

        approveFromPreview() {
            if (this.previewData) {
                this.showPreviewModal = false;
                this.approveArticle(this.previewData.id);
            }
        },

        revisionFromPreview() {
            if (this.previewData) {
                this.showPreviewModal = false;
                this.openRevisionModal(this.previewData.id, this.previewData.title);
            }
        },

        rejectFromPreview() {
            if (this.previewData) {
                this.showPreviewModal = false;
                this.openRejectModal(this.previewData.id, this.previewData.title);
            }
        }
    }
}
</script>
@endsection
