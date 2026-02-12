@extends('layouts.app')

@section('title', 'Admin Dashboard - IDN Menulis')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Admin Dashboard</h1>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Sistem Aktif
                    </span>
                </div>
                <p class="text-gray-600 mt-1">Kelola platform IDN Menulis</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>

        <!-- Main Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">
                        +{{ $userStats['new_this_month'] }} bulan ini
                    </span>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($userStats['total']) }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Pengguna</p>
                <div class="flex gap-2 mt-3 text-xs">
                    <span class="text-blue-600">{{ $userStats['siswa'] }} Siswa</span>
                    <span class="text-gray-300">•</span>
                    <span class="text-teal-600">{{ $userStats['guru'] }} Guru</span>
                    <span class="text-gray-300">•</span>
                    <span class="text-purple-600">{{ $userStats['admin'] }} Admin</span>
                </div>
            </div>

            <!-- Total Articles -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-teal-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($articleStats['total']) }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Artikel</p>
                <p class="text-xs text-teal-600 mt-3">{{ number_format($articleStats['total_views']) }} total views</p>
            </div>

            <!-- Pending Review -->
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-sm border border-amber-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    @if($articleStats['pending'] > 0)
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                    @endif
                </div>
                <p class="text-3xl font-bold text-amber-700">{{ number_format($articleStats['pending']) }}</p>
                <p class="text-sm text-amber-600 mt-1">Pending Review</p>
                <a href="{{ route('approvals.pending') }}" class="inline-flex items-center text-xs text-amber-700 hover:text-amber-800 mt-3 font-medium">
                    Review sekarang
                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <!-- Categories -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $categories->count() }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Kategori</p>
                <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center text-xs text-purple-600 hover:text-purple-700 mt-3 font-medium">
                    Kelola kategori
                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <!-- Engagement -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-rose-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($engagementStats['total_likes']) }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Likes</p>
                <p class="text-xs text-gray-400 mt-3">{{ number_format($engagementStats['total_comments']) }} komentar</p>
            </div>
        </div>

        <!-- Quick Access -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('admin.users.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg hover:border-blue-200 transition-all">
                <div class="w-12 h-12 rounded-xl bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center mb-3 transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">Kelola Pengguna</p>
                <p class="text-sm text-gray-500 mt-1">{{ $userStats['total'] }} pengguna</p>
            </a>

            <a href="{{ route('admin.categories.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg hover:border-purple-200 transition-all">
                <div class="w-12 h-12 rounded-xl bg-purple-50 group-hover:bg-purple-100 flex items-center justify-center mb-3 transition-colors">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-900 group-hover:text-purple-600 transition-colors">Kelola Kategori</p>
                <p class="text-sm text-gray-500 mt-1">{{ $categories->count() }} kategori</p>
            </a>

            <a href="{{ route('approvals.pending') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg hover:border-amber-200 transition-all">
                <div class="w-12 h-12 rounded-xl bg-amber-50 group-hover:bg-amber-100 flex items-center justify-center mb-3 transition-colors">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-900 group-hover:text-amber-600 transition-colors">Persetujuan Artikel</p>
                <p class="text-sm text-gray-500 mt-1">{{ $articleStats['pending'] }} menunggu</p>
            </a>

            <a href="{{ route('admin.activity-logs') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg hover:border-teal-200 transition-all">
                <div class="w-12 h-12 rounded-xl bg-teal-50 group-hover:bg-teal-100 flex items-center justify-center mb-3 transition-colors">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-900 group-hover:text-teal-600 transition-colors">Laporan Sistem</p>
                <p class="text-sm text-gray-500 mt-1">Log aktivitas</p>
            </a>
        </div>

        <!-- Charts & Data Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Article Trend Chart -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Tren Artikel</h2>
                        <p class="text-sm text-gray-500">6 bulan terakhir</p>
                    </div>
                </div>
                <div class="h-64" id="articleChart">
                    <div class="flex items-end justify-between h-full gap-4 px-4">
                        @php
                            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                            $maxCount = $monthlyArticles->max('count') ?: 1;
                        @endphp
                        @forelse($monthlyArticles as $data)
                            @php
                                $height = ($data->count / $maxCount) * 100;
                                $monthName = $months[$data->month - 1] ?? '';
                            @endphp
                            <div class="flex-1 flex flex-col items-center gap-2">
                                <div class="w-full bg-gradient-to-t from-teal-500 to-teal-400 rounded-t-lg transition-all hover:from-teal-600 hover:to-teal-500"
                                     style="height: {{ max($height, 8) }}%"
                                     title="{{ $data->count }} artikel">
                                </div>
                                <span class="text-xs text-gray-500">{{ $monthName }}</span>
                                <span class="text-xs font-medium text-gray-700">{{ $data->count }}</span>
                            </div>
                        @empty
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <p>Belum ada data</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Kategori Terpopuler</h2>
                        <p class="text-sm text-gray-500">Berdasarkan jumlah artikel</p>
                    </div>
                </div>
                <div class="space-y-4">
                    @php $totalCategoryArticles = $categories->sum('articles_count') ?: 1; @endphp
                    @foreach($categories->take(5) as $category)
                        @php $percentage = round(($category->articles_count / $totalCategoryArticles) * 100); @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ $category->name }}</span>
                                <span class="text-sm text-gray-500">{{ $category->articles_count }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-gradient-to-r from-teal-500 to-blue-500 h-2 rounded-full transition-all"
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Activity & Users -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Activities -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h2>
                        <p class="text-sm text-gray-500">Log sistem terkini</p>
                    </div>
                    <a href="{{ route('admin.activity-logs') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                        Lihat Semua
                    </a>
                </div>
                <div class="divide-y divide-gray-50 max-h-96 overflow-y-auto">
                    @forelse($recentActivities as $activity)
                        <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-medium text-gray-600">
                                        {{ substr($activity->user->full_name ?? 'S', 0, 1) }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $activity->description }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $activity->user->full_name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-gray-500">Belum ada aktivitas</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Users -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Pengguna Terbaru</h2>
                        <p class="text-sm text-gray-500">Pendaftaran terkini</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                        Lihat Semua
                    </a>
                </div>
                <div class="divide-y divide-gray-50 max-h-96 overflow-y-auto">
                    @forelse($recentUsers as $user)
                        <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-teal-400 to-blue-500 flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">
                                            {{ substr($user->full_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $user->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($user->role === 'admin') bg-purple-100 text-purple-700
                                        @elseif($user->role === 'guru') bg-blue-100 text-blue-700
                                        @else bg-teal-100 text-teal-700
                                        @endif">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                    <p class="text-xs text-gray-400 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="text-gray-500">Belum ada pengguna</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Authors -->
        <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Penulis Terbaik</h2>
                    <p class="text-sm text-gray-500">Berdasarkan jumlah artikel terpublikasi</p>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                @foreach($topAuthors as $index => $author)
                    <div class="text-center p-4 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors">
                        <div class="relative inline-block mb-3">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-teal-400 to-blue-500 flex items-center justify-center mx-auto">
                                <span class="text-white text-xl font-bold">{{ substr($author->full_name, 0, 1) }}</span>
                            </div>
                            @if($index < 3)
                                <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                    @if($index === 0) bg-yellow-400 text-yellow-900
                                    @elseif($index === 1) bg-gray-300 text-gray-700
                                    @else bg-amber-600 text-white
                                    @endif">
                                    {{ $index + 1 }}
                                </span>
                            @endif
                        </div>
                        <p class="font-medium text-gray-900 text-sm truncate">{{ $author->full_name }}</p>
                        <p class="text-xs text-gray-500">{{ $author->articles_count }} artikel</p>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
