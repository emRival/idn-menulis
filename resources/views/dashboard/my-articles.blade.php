@extends('layouts.app')

@section('title', 'Artikel Saya - IDN Menulis')

@section('styles')
<style>
    .stat-card {
        transition: all 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .article-row {
        transition: all 0.15s ease;
    }
    .article-row:hover {
        background-color: #f8fafc;
    }
    .filter-btn {
        transition: all 0.15s ease;
    }
    .filter-btn.active {
        background-color: #3b82f6;
        color: white;
    }
    .filter-btn:not(.active):hover {
        background-color: #f1f5f9;
    }
    .bulk-toolbar {
        position: sticky;
        bottom: 0;
        background: white;
        border-top: 1px solid #e5e7eb;
        box-shadow: 0 -4px 20px -5px rgba(0, 0, 0, 0.1);
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }
    .bulk-toolbar.show {
        transform: translateY(0);
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
    }
    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        bottom: 100%;
        margin-bottom: 0.25rem;
        min-width: 180px;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 -10px 40px -10px rgba(0, 0, 0, 0.25);
        z-index: 100;
        overflow: hidden;
    }
    .dropdown {
        position: relative;
    }
    .article-row {
        overflow: visible;
    }
    .dropdown:hover .dropdown-menu,
    .dropdown-menu:hover {
        display: block;
    }
    [x-cloak] { display: none !important; }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50" x-data="articlesManager()">
    <!-- Sticky Header -->
    <div class="sticky top-0 z-40 bg-white border-b shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <nav class="text-sm text-gray-500 mb-1">
                        <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                        <span class="mx-2">›</span>
                        <span class="text-gray-900">Articles</span>
                    </nav>
                    <h1 class="text-2xl font-bold text-gray-900">Artikel Saya</h1>
                </div>
                <a href="{{ route('articles.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-600/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tulis Artikel Baru
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="stat-card bg-white p-4 rounded-xl border shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                        <p class="text-xs text-gray-500">Total Artikel</p>
                    </div>
                </div>
            </div>
            <div class="stat-card bg-white p-4 rounded-xl border shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['published'] }}</p>
                        <p class="text-xs text-gray-500">Published</p>
                    </div>
                </div>
            </div>
            <div class="stat-card bg-white p-4 rounded-xl border shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['scheduled'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Dijadwalkan</p>
                    </div>
                </div>
            </div>
            <div class="stat-card bg-white p-4 rounded-xl border shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                        <p class="text-xs text-gray-500">Pending</p>
                    </div>
                </div>
            </div>
            <div class="stat-card bg-white p-4 rounded-xl border shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['draft'] }}</p>
                        <p class="text-xs text-gray-500">Draft</p>
                    </div>
                </div>
            </div>
            <div class="stat-card bg-white p-4 rounded-xl border shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_views']) }}</p>
                        <p class="text-xs text-gray-500">Total Views</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-white rounded-xl border shadow-sm mb-6 overflow-hidden">
            <form action="{{ route('dashboard.articles') }}" method="GET" class="p-4">
                <div class="flex flex-col lg:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1 relative">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari judul artikel..."
                               class="w-full pl-10 pr-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Category Filter -->
                    <select name="category" class="px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Sort -->
                    <select name="sort" class="px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>A-Z</option>
                    </select>

                    <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                        Filter
                    </button>
                </div>
            </form>

            <!-- Status Tabs -->
            <div class="px-4 pb-4 flex flex-wrap gap-2">
                <a href="{{ route('dashboard.articles') }}"
                   class="filter-btn px-4 py-2 rounded-lg text-sm font-medium {{ !request('status') || request('status') == 'all' ? 'active' : '' }}">
                    Semua ({{ $stats['total'] }})
                </a>
                <a href="{{ route('dashboard.articles', ['status' => 'draft'] + request()->except('status', 'page')) }}"
                   class="filter-btn px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'draft' ? 'active' : '' }}">
                    Draft ({{ $stats['draft'] }})
                </a>
                <a href="{{ route('dashboard.articles', ['status' => 'pending'] + request()->except('status', 'page')) }}"
                   class="filter-btn px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'pending' ? 'active' : '' }}">
                    Pending ({{ $stats['pending'] }})
                </a>
                <a href="{{ route('dashboard.articles', ['status' => 'revision'] + request()->except('status', 'page')) }}"
                   class="filter-btn px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'revision' ? 'active' : '' }}">
                    Revisi ({{ $stats['revision'] }})
                </a>
                <a href="{{ route('dashboard.articles', ['status' => 'published'] + request()->except('status', 'page')) }}"
                   class="filter-btn px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'published' ? 'active' : '' }}">
                    Published ({{ $stats['published'] }})
                </a>
                <a href="{{ route('dashboard.articles', ['status' => 'scheduled'] + request()->except('status', 'page')) }}"
                   class="filter-btn px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'scheduled' ? 'active' : '' }}">
                    Dijadwalkan ({{ $stats['scheduled'] ?? 0 }})
                </a>
                <a href="{{ route('dashboard.articles', ['status' => 'rejected'] + request()->except('status', 'page')) }}"
                   class="filter-btn px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'rejected' ? 'active' : '' }}">
                    Ditolak ({{ $stats['rejected'] }})
                </a>
            </div>
        </div>

        <!-- Articles List -->
        <div class="bg-white rounded-xl border shadow-sm">
            @if($articles->count() > 0)
                <!-- Select All Header -->
                <div class="px-4 md:px-6 py-3 bg-gray-50 border-b flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()"
                               class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-600">Pilih Semua</span>
                    </label>
                    <span class="text-sm text-gray-400" x-show="selectedCount > 0" x-cloak>
                        (<span x-text="selectedCount"></span> terpilih)
                    </span>
                </div>

                <!-- Desktop Table Header (hidden on mobile) -->
                <div class="hidden lg:grid lg:grid-cols-12 gap-4 px-4 py-3 bg-gray-50 border-b text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    <div class="col-span-1"></div>
                    <div class="col-span-4">Artikel</div>
                    <div class="col-span-2">Kategori</div>
                    <div class="col-span-1">Status</div>
                    <div class="col-span-1 text-center">Views</div>
                    <div class="col-span-1 text-center">Likes</div>
                    <div class="col-span-1">Diperbarui</div>
                    <div class="col-span-1 text-right">Aksi</div>
                </div>

                <!-- Articles List -->
                <div class="divide-y divide-gray-100">
                    @foreach($articles as $article)
                        <!-- Article Row -->
                        <div class="article-row p-4">
                            <div class="flex flex-col lg:grid lg:grid-cols-12 gap-4 lg:items-center">
                                <!-- Checkbox -->
                                <div class="hidden lg:block lg:col-span-1">
                                    <input type="checkbox" value="{{ $article->id }}" x-model="selectedArticles"
                                           class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </div>

                                <!-- Article Info -->
                                <div class="lg:col-span-4">
                                    <div class="flex items-start gap-3">
                                        <!-- Mobile Checkbox -->
                                        <div class="lg:hidden pt-1">
                                            <input type="checkbox" value="{{ $article->id }}" x-model="selectedArticles"
                                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </div>
                                        <!-- Thumbnail -->
                                        <div class="flex-shrink-0">
                                            @if($article->featured_image)
                                                <img src="{{ Storage::url($article->featured_image) }}"
                                                     alt="{{ $article->title }}"
                                                     class="w-16 h-12 object-cover rounded-lg">
                                            @else
                                                <div class="w-16 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <!-- Title & Excerpt -->
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-900 line-clamp-1">
                                                @if($article->status === 'published')
                                                    <a href="{{ route('articles.show', $article) }}" class="hover:text-blue-600">{{ $article->title }}</a>
                                                @else
                                                    {{ $article->title }}
                                                @endif
                                            </h4>
                                            <p class="text-sm text-gray-500 line-clamp-1">{{ Str::limit($article->excerpt, 60) }}</p>
                                            <!-- Mobile: Inline meta -->
                                            <div class="flex flex-wrap items-center gap-2 mt-2 lg:hidden">
                                                @if($article->category)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                        {{ $article->category->name }}
                                                    </span>
                                                @endif
                                                @if($article->isScheduled())
                                                    <span class="status-badge bg-purple-100 text-purple-700">Dijadwalkan</span>
                                                @else
                                                    @switch($article->status)
                                                        @case('published')
                                                            <span class="status-badge bg-green-100 text-green-700">Published</span>
                                                            @break
                                                        @case('pending')
                                                            <span class="status-badge bg-yellow-100 text-yellow-700">Pending</span>
                                                            @break
                                                        @case('draft')
                                                            <span class="status-badge bg-gray-100 text-gray-700">Draft</span>
                                                            @break
                                                        @case('revision')
                                                            <span class="status-badge bg-blue-100 text-blue-700">Revisi</span>
                                                            @break
                                                        @case('rejected')
                                                            <span class="status-badge bg-red-100 text-red-700">Ditolak</span>
                                                            @break
                                                    @endswitch
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Category (Desktop only) -->
                                <div class="hidden lg:block lg:col-span-2">
                                    @if($article->category)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ $article->category->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </div>

                                <!-- Status (Desktop only) -->
                                <div class="hidden lg:block lg:col-span-1">
                                    @if($article->isScheduled())
                                        <span class="status-badge bg-purple-100 text-purple-700">Dijadwalkan</span>
                                    @else
                                        @switch($article->status)
                                            @case('published')
                                                <span class="status-badge bg-green-100 text-green-700">Published</span>
                                                @break
                                            @case('pending')
                                                <span class="status-badge bg-yellow-100 text-yellow-700">Pending</span>
                                                @break
                                            @case('draft')
                                                <span class="status-badge bg-gray-100 text-gray-700">Draft</span>
                                                @break
                                            @case('revision')
                                                <span class="status-badge bg-blue-100 text-blue-700">Revisi</span>
                                                @break
                                            @case('rejected')
                                                <span class="status-badge bg-red-100 text-red-700">Ditolak</span>
                                                @break
                                        @endswitch
                                    @endif
                                </div>

                                <!-- Stats & Actions Row -->
                                <div class="flex items-center justify-between lg:contents mt-3 lg:mt-0 pl-7 lg:pl-0">
                                    <!-- Stats -->
                                    <div class="flex items-center gap-4 lg:contents">
                                        <!-- Views -->
                                        <div class="flex items-center gap-1 lg:col-span-1 lg:justify-center">
                                            <svg class="w-4 h-4 text-gray-400 lg:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">{{ number_format($article->views_count) }}</span>
                                        </div>

                                        <!-- Likes -->
                                        <div class="flex items-center gap-1 lg:col-span-1 lg:justify-center">
                                            <svg class="w-4 h-4 text-gray-400 lg:hidden" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">{{ $article->likedBy()->count() }}</span>
                                        </div>

                                        <!-- Updated (Desktop only) -->
                                        <div class="hidden lg:block lg:col-span-1">
                                            @if($article->isScheduled())
                                                <span class="text-sm text-purple-600" title="Dijadwalkan pada {{ $article->scheduled_at->format('d M Y H:i') }}">
                                                    {{ $article->scheduled_at->diffForHumans() }}
                                                </span>
                                            @else
                                                <span class="text-sm text-gray-500">{{ $article->updated_at->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center gap-1 lg:col-span-1 lg:justify-end">
                                        <!-- Edit -->
                                        <a href="{{ route('articles.edit', $article) }}"
                                           class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        <!-- Preview -->
                                        @if($article->status === 'published')
                                            <a href="{{ route('articles.show', $article) }}"
                                               class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition" title="Lihat">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                        @endif

                                        <!-- Dropdown Menu -->
                                        <div class="dropdown relative">
                                            <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                                </svg>
                                            </button>
                                            <div class="dropdown-menu py-1">
                                                <!-- Submit Review (for draft/revision) -->
                                                @if(in_array($article->status, ['draft', 'revision']))
                                                    <form action="{{ route('articles.publish', $article) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                            </svg>
                                                            Ajukan Review
                                                        </button>
                                                    </form>
                                                @endif
                                                <!-- Duplicate -->
                                                <form action="{{ route('articles.duplicate', $article) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                        </svg>
                                                        Duplikasi
                                                    </button>
                                                </form>
                                                <!-- Delete -->
                                                <form action="{{ route('articles.destroy', $article) }}" method="POST"
                                                      onsubmit="return confirm('Hapus artikel ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reviewer Notes (if rejected/revision) -->
                            @if(in_array($article->status, ['rejected', 'revision']) && $article->rejection_reason)
                                <div class="mt-3 ml-7 lg:ml-0 p-3 bg-red-50 rounded-lg">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <div>
                                            <p class="text-xs font-medium text-red-700">Catatan Reviewer:</p>
                                            <p class="text-sm text-red-600">{{ $article->rejection_reason }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($articles->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $articles->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="py-16 text-center">
                    <svg class="w-20 h-20 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Artikel</h3>
                    <p class="text-gray-500 mb-6">Mulai menulis dan bagikan ide-idemu kepada dunia!</p>
                    <a href="{{ route('articles.create') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-600/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tulis Artikel Pertama
                    </a>
                </div>
            @endif
        </div>

        <!-- Bulk Action Toolbar -->
        <div class="bulk-toolbar fixed bottom-0 left-0 right-0 py-4 px-6 flex items-center justify-between"
             :class="selectedCount > 0 ? 'show' : ''" x-cloak>
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-700">
                    <span x-text="selectedCount"></span> artikel terpilih
                </span>
            </div>
            <div class="flex items-center gap-3">
                <!-- Bulk Submit -->
                <form action="{{ route('articles.bulk-submit') }}" method="POST" x-ref="bulkSubmitForm">
                    @csrf
                    <template x-for="id in selectedArticles">
                        <input type="hidden" name="article_ids[]" :value="id">
                    </template>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Submit Review
                    </button>
                </form>

                <!-- Bulk Delete -->
                <form action="{{ route('articles.bulk-delete') }}" method="POST" x-ref="bulkDeleteForm"
                      onsubmit="return confirm('Hapus semua artikel yang dipilih?')">
                    @csrf
                    <template x-for="id in selectedArticles">
                        <input type="hidden" name="article_ids[]" :value="id">
                    </template>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus
                    </button>
                </form>

                <!-- Cancel -->
                <button @click="clearSelection()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-6 right-6 z-50 bg-green-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif
@endsection

@section('scripts')
<script>
function articlesManager() {
    return {
        selectedArticles: [],
        selectAll: false,

        get selectedCount() {
            return this.selectedArticles.length;
        },

        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedArticles = @json($articles->pluck('id'));
            } else {
                this.selectedArticles = [];
            }
        },

        clearSelection() {
            this.selectedArticles = [];
            this.selectAll = false;
        }
    }
}
</script>
@endsection
