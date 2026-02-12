@extends('layouts.app')

@section('title', 'Dashboard Saya - IDN Menulis')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-cyan-50 to-teal-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard Saya</h1>
                    @if($writingStreak > 0)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-orange-400 to-red-500 text-white">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
                        </svg>
                        {{ $writingStreak }} Hari Streak!
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        </svg>
                        Penulis Aktif
                    </span>
                    @endif
                </div>
                <p class="text-gray-600 mt-1">Selamat datang kembali, {{ auth()->user()->full_name }}!</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('articles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-xl font-medium transition-all shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tulis Artikel Baru
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <!-- Total Articles -->
            <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl shadow-lg p-5 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="relative">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">{{ $stats['total'] }}</p>
                    <p class="text-white/80 mt-1 text-sm">Total Artikel</p>
                </div>
            </div>

            <!-- Pending -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    @if($stats['pending'] > 0)
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                    </span>
                    @endif
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Menunggu Review</p>
            </div>

            <!-- Published -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['published'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Dipublikasi</p>
            </div>

            <!-- Total Views -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalViews) }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Views</p>
            </div>

            <!-- Total Likes -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-xl bg-pink-100 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalLikes) }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Suka</p>
            </div>
        </div>

        <!-- Monthly Progress -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Progress Menulis Bulan Ini</h2>
                    <p class="text-sm text-gray-500">Target: {{ $monthlyTarget }} artikel per bulan</p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-blue-600">{{ $articlesThisMonth }}<span class="text-lg text-gray-400">/{{ $monthlyTarget }}</span></p>
                    <p class="text-sm text-gray-500">Artikel bulan ini</p>
                </div>
            </div>
            <div class="relative">
                <div class="overflow-hidden h-4 rounded-full bg-blue-100">
                    @php
                        $progress = min(($articlesThisMonth / $monthlyTarget) * 100, 100);
                    @endphp
                    <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 transition-all duration-500" style="width: {{ $progress }}%"></div>
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-500">
                    <span>0</span>
                    <span>{{ round($progress) }}% tercapai</span>
                    <span>{{ $monthlyTarget }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Access -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('articles.create') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg hover:border-blue-200 transition-all text-center">
                <div class="w-14 h-14 rounded-xl bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center mx-auto mb-3 transition-colors">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">Tulis Artikel</p>
                <p class="text-xs text-gray-500 mt-1">Buat artikel baru</p>
            </a>

            <a href="{{ route('dashboard.articles') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg hover:border-emerald-200 transition-all text-center">
                <div class="w-14 h-14 rounded-xl bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center mx-auto mb-3 transition-colors">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-900 group-hover:text-emerald-600 transition-colors">Artikel Saya</p>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['total'] }} artikel</p>
            </a>

            <a href="{{ route('bookmarks.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg hover:border-purple-200 transition-all text-center">
                <div class="w-14 h-14 rounded-xl bg-purple-50 group-hover:bg-purple-100 flex items-center justify-center mx-auto mb-3 transition-colors">
                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-900 group-hover:text-purple-600 transition-colors">Bookmark</p>
                <p class="text-xs text-gray-500 mt-1">{{ $totalBookmarks }} tersimpan</p>
            </a>

            <a href="{{ route('profile.edit') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg hover:border-pink-200 transition-all text-center">
                <div class="w-14 h-14 rounded-xl bg-pink-50 group-hover:bg-pink-100 flex items-center justify-center mx-auto mb-3 transition-colors">
                    <svg class="w-7 h-7 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-900 group-hover:text-pink-600 transition-colors">Profil Saya</p>
                <p class="text-xs text-gray-500 mt-1">Edit profil</p>
            </a>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Revision Notifications -->
                @if($revisionArticles->count() > 0)
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-amber-900">Artikel Perlu Revisi</h3>
                            <p class="text-sm text-amber-700 mt-1">{{ $revisionArticles->count() }} artikel membutuhkan perbaikan</p>
                            <div class="mt-4 space-y-2">
                                @foreach($revisionArticles->take(3) as $article)
                                <a href="{{ route('articles.edit', $article) }}" class="block bg-white rounded-lg p-3 hover:bg-amber-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-900 truncate">{{ $article->title }}</span>
                                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Recent Articles -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Artikel Terbaru Saya</h2>
                            <p class="text-sm text-gray-500">Artikel yang baru saja Anda tulis</p>
                        </div>
                        <a href="{{ route('dashboard.articles') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($recentArticles as $article)
                            <a href="{{ route('articles.show', $article) }}" class="block px-6 py-4 hover:bg-blue-50/50 transition-colors">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-gray-900 truncate hover:text-blue-600">
                                            {{ $article->title }}
                                        </h3>
                                        <div class="flex flex-wrap items-center gap-2 mt-2 text-sm">
                                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                                @if($article->status === 'published') bg-emerald-100 text-emerald-700
                                                @elseif($article->status === 'pending') bg-amber-100 text-amber-700
                                                @elseif($article->status === 'revision') bg-purple-100 text-purple-700
                                                @elseif($article->status === 'draft') bg-gray-100 text-gray-700
                                                @else bg-red-100 text-red-700
                                                @endif">
                                                {{ ucfirst($article->status) }}
                                            </span>
                                            <span class="text-gray-400">•</span>
                                            <span class="text-gray-500">{{ $article->created_at->diffForHumans() }}</span>
                                            @if($article->status === 'published')
                                            <span class="text-gray-400">•</span>
                                            <span class="text-gray-500 flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                {{ number_format($article->views_count ?? 0) }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </div>
                                <p class="text-gray-600 font-medium">Belum ada artikel</p>
                                <p class="text-sm text-gray-400 mt-1">Mulai menulis artikel pertamamu!</p>
                                <a href="{{ route('articles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 mt-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Tulis Artikel
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Draft Articles -->
                @if($draftArticles->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Draft Tersimpan</h2>
                        <p class="text-sm text-gray-500">Lanjutkan menulis draft Anda</p>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($draftArticles as $article)
                            <a href="{{ route('articles.edit', $article) }}" class="block px-6 py-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-gray-900 truncate">{{ $article->title }}</h3>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Terakhir diubah {{ $article->updated_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <button class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                                        Lanjutkan
                                    </button>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Top Performing Articles -->
                @if($topArticles->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Artikel Terpopuler</h2>
                        <p class="text-sm text-gray-500">Artikel dengan views tertinggi</p>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($topArticles as $index => $article)
                            <a href="{{ route('articles.show', $article) }}" class="block px-6 py-4 hover:bg-purple-50/50 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm
                                        @if($index === 0) bg-yellow-100 text-yellow-700
                                        @elseif($index === 1) bg-gray-100 text-gray-600
                                        @elseif($index === 2) bg-amber-100 text-amber-700
                                        @else bg-gray-50 text-gray-500
                                        @endif">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate">{{ $article->title }}</p>
                                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ number_format($article->views_count ?? 0) }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-pink-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ $article->likes_count ?? 0 }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Engagement Stats -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Statistik Engagement</h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <span class="text-gray-600">Views</span>
                            </div>
                            <span class="font-semibold text-gray-900">{{ number_format($totalViews) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-pink-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-gray-600">Suka</span>
                            </div>
                            <span class="font-semibold text-gray-900">{{ number_format($totalLikes) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                </div>
                                <span class="text-gray-600">Komentar</span>
                            </div>
                            <span class="font-semibold text-gray-900">{{ number_format($totalComments) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                    </svg>
                                </div>
                                <span class="text-gray-600">Bookmark</span>
                            </div>
                            <span class="font-semibold text-gray-900">{{ number_format($totalBookmarks) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Recommended Articles -->
                @if($recommendedArticles->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Rekomendasi Bacaan</h2>
                        <p class="text-sm text-gray-500">Artikel yang mungkin Anda suka</p>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($recommendedArticles as $article)
                            <a href="{{ route('articles.show', $article) }}" class="block px-6 py-4 hover:bg-blue-50/50 transition-colors">
                                <p class="font-medium text-gray-900 hover:text-blue-600 line-clamp-2">{{ $article->title }}</p>
                                <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                                    <span>{{ $article->user->full_name }}</span>
                                    <span>•</span>
                                    <span>{{ $article->published_at?->diffForHumans() }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Motivational Card -->
                <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl shadow-lg p-6 text-white">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Tips Menulis</h3>
                    <p class="text-white/80 text-sm">
                        @php
                            $tips = [
                                'Mulai dengan outline sebelum menulis untuk membantu mengorganisir ide.',
                                'Baca ulang tulisanmu sebelum mengirim untuk menghindari typo.',
                                'Gunakan kalimat pendek dan jelas agar mudah dipahami pembaca.',
                                'Tambahkan gambar untuk membuat artikelmu lebih menarik.',
                                'Konsisten menulis setiap hari untuk meningkatkan skill menulismu.',
                            ];
                            $randomTip = $tips[array_rand($tips)];
                        @endphp
                        {{ $randomTip }}
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
