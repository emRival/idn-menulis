@extends('layouts.app')

@section('title', 'Dashboard Guru - IDN Menulis')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard Guru</h1>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Reviewer Aktif
                    </span>
                </div>
                <p class="text-gray-600 mt-1">Review artikel siswa dan pantau aktivitas literasi</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('approvals.pending') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Review Artikel
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Pending Review -->
            <div class="bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl shadow-lg p-5 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        @if($stats['pending'] > 0)
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                        </span>
                        @endif
                    </div>
                    <p class="text-4xl font-bold">{{ $stats['pending'] }}</p>
                    <p class="text-white/80 mt-1">Artikel Pending</p>
                </div>
            </div>

            <!-- Revision -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['revision'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Perlu Revisi</p>
            </div>

            <!-- Published Today -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['published_today'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Dipublikasi Hari Ini</p>
            </div>

            <!-- Active Students -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['active_siswa'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Siswa Aktif</p>
            </div>
        </div>

        <!-- Your Review Stats -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Statistik Review Anda</h2>
                    <p class="text-sm text-gray-500">Total kontribusi Anda dalam review artikel</p>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="text-center p-4 rounded-xl bg-emerald-50">
                    <p class="text-3xl font-bold text-emerald-600">{{ $stats['total_approved'] }}</p>
                    <p class="text-sm text-emerald-700 mt-1">Disetujui</p>
                </div>
                <div class="text-center p-4 rounded-xl bg-red-50">
                    <p class="text-3xl font-bold text-red-600">{{ $stats['total_rejected'] }}</p>
                    <p class="text-sm text-red-700 mt-1">Ditolak</p>
                </div>
                <div class="text-center p-4 rounded-xl bg-blue-50">
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_approved'] + $stats['total_rejected'] }}</p>
                    <p class="text-sm text-blue-700 mt-1">Total Review</p>
                </div>
                <div class="text-center p-4 rounded-xl bg-amber-50">
                    <p class="text-3xl font-bold text-amber-600">
                        @if($stats['total_approved'] + $stats['total_rejected'] > 0)
                            {{ round(($stats['total_approved'] / ($stats['total_approved'] + $stats['total_rejected'])) * 100) }}%
                        @else
                            0%
                        @endif
                    </p>
                    <p class="text-sm text-amber-700 mt-1">Rate Approval</p>
                </div>
            </div>
        </div>

        <!-- Quick Access -->
        <div class="grid grid-cols-3 gap-4 mb-8">
            <a href="{{ route('approvals.pending') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg hover:border-emerald-200 transition-all text-center">
                <div class="w-14 h-14 rounded-xl bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center mx-auto mb-3 transition-colors">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-900 group-hover:text-emerald-600 transition-colors">Review Artikel</p>
                <p class="text-sm text-gray-500 mt-1">{{ $stats['pending'] }} menunggu</p>
            </a>

            <a href="{{ route('approvals.history') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg hover:border-blue-200 transition-all text-center">
                <div class="w-14 h-14 rounded-xl bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center mx-auto mb-3 transition-colors">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">Riwayat Review</p>
                <p class="text-sm text-gray-500 mt-1">Lihat histori</p>
            </a>

            <a href="{{ route('articles.index') }}" class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg hover:border-purple-200 transition-all text-center">
                <div class="w-14 h-14 rounded-xl bg-purple-50 group-hover:bg-purple-100 flex items-center justify-center mx-auto mb-3 transition-colors">
                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-900 group-hover:text-purple-600 transition-colors">Semua Artikel</p>
                <p class="text-sm text-gray-500 mt-1">Publikasi terbaru</p>
            </a>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Pending Articles -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Articles Waiting Review -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Artikel Menunggu Review</h2>
                            <p class="text-sm text-gray-500">Klik untuk mereview artikel</p>
                        </div>
                        <a href="{{ route('approvals.pending') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($pendingArticles as $article)
                            <a href="{{ route('approvals.show', $article->id) }}" class="block px-6 py-4 hover:bg-emerald-50/50 transition-colors">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-gray-900 truncate hover:text-emerald-600">
                                            {{ $article->title }}
                                        </h3>
                                        <div class="flex items-center gap-2 mt-2 text-sm text-gray-500">
                                            <div class="flex items-center gap-1">
                                                <div class="w-5 h-5 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center">
                                                    <span class="text-white text-xs font-medium">{{ substr($article->user->full_name, 0, 1) }}</span>
                                                </div>
                                                <span>{{ $article->user->full_name }}</span>
                                            </div>
                                            <span class="text-gray-300">•</span>
                                            <span>{{ $article->category->name ?? 'Tanpa Kategori' }}</span>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">
                                            Diajukan {{ $article->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <button class="flex-shrink-0 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors">
                                        Review
                                    </button>
                                </div>
                            </a>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-600 font-medium">Semua artikel sudah direview!</p>
                                <p class="text-sm text-gray-400 mt-1">Tidak ada artikel yang menunggu review</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Articles Need Revision -->
                @if($revisionArticles->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Artikel Perlu Revisi</h2>
                        <p class="text-sm text-gray-500">Siswa sedang merevisi artikel ini</p>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($revisionArticles as $article)
                            <div class="px-6 py-4 hover:bg-purple-50/50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $article->title }}</h3>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ $article->user->full_name }} • {{ $article->updated_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">
                                        Menunggu Revisi
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Recent Approvals -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Review Terbaru Anda</h2>
                            <p class="text-sm text-gray-500">Histori review artikel</p>
                        </div>
                        <a href="{{ route('approvals.history') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($recentApprovals as $approval)
                            <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate">
                                            {{ $approval->article->title ?? 'Artikel dihapus' }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-1 text-sm text-gray-500">
                                            <span>{{ $approval->article->user->full_name ?? '-' }}</span>
                                            <span class="text-gray-300">•</span>
                                            <span class="inline-flex items-center gap-1
                                                @if($approval->new_status === 'published') text-emerald-600
                                                @elseif($approval->new_status === 'revision') text-purple-600
                                                @else text-red-600
                                                @endif">
                                                @if($approval->new_status === 'published')
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Disetujui
                                                @elseif($approval->new_status === 'revision')
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                                    </svg>
                                                    Perlu Revisi
                                                @else
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Ditolak
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400 flex-shrink-0">
                                        {{ $approval->reviewed_at?->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-gray-500">Belum ada review</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Pending by Category -->
                @if($pendingByCategory->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Pending per Kategori</h2>
                    <div class="space-y-3">
                        @foreach($pendingByCategory as $item)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">{{ $item->category->name ?? 'Tanpa Kategori' }}</span>
                                <span class="px-2.5 py-0.5 bg-amber-100 text-amber-700 text-xs font-medium rounded-full">
                                    {{ $item->count }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Top Authors -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Penulis Teraktif</h2>
                        <p class="text-sm text-gray-500">Siswa dengan artikel terbanyak</p>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($topAuthors as $index => $author)
                            <div class="px-6 py-4 flex items-center gap-4">
                                <div class="relative">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center">
                                        <span class="text-white text-sm font-bold">{{ substr($author->full_name, 0, 1) }}</span>
                                    </div>
                                    @if($index < 3)
                                        <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold
                                            @if($index === 0) bg-yellow-400 text-yellow-900
                                            @elseif($index === 1) bg-gray-300 text-gray-700
                                            @else bg-amber-600 text-white
                                            @endif">
                                            {{ $index + 1 }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $author->full_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $author->articles_count }} artikel</p>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-500">
                                <p>Belum ada data</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Motivational Card -->
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg p-6 text-white">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Terima Kasih, Guru!</h3>
                    <p class="text-white/80 text-sm">
                        Kontribusi Anda dalam mereview artikel sangat berarti bagi perkembangan literasi siswa.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
