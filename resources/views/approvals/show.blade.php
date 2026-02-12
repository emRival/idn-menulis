@extends('layouts.app')

@section('title', 'Review Artikel - IDN Menulis')

@section('styles')
<style>
    .prose img {
        max-width: 100%;
        border-radius: 8px;
    }
    .action-card {
        transition: all 0.2s ease;
    }
    .action-card:hover {
        transform: translateY(-2px);
    }
    .modal-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50" x-data="reviewManager()">
    <!-- Sticky Header -->
    <div class="sticky top-0 z-40 bg-white border-b shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('approvals.pending') }}"
                       class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <nav class="flex items-center text-sm text-gray-500 mb-1">
                            <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <a href="{{ route('approvals.pending') }}" class="hover:text-blue-600">Persetujuan</a>
                            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <span class="text-gray-700 font-medium">Review</span>
                        </nav>
                        <h1 class="text-lg font-bold text-gray-900">{{ Str::limit($article->title, 50) }}</h1>
                    </div>
                </div>

                <!-- Status Badge -->
                @php
                    $statusClasses = [
                        'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                        'pending_review' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                        'revision' => 'bg-blue-100 text-blue-700 border-blue-200',
                        'published' => 'bg-green-100 text-green-700 border-green-200',
                        'rejected' => 'bg-red-100 text-red-700 border-red-200',
                    ];
                    $statusLabels = [
                        'pending' => 'Menunggu Review',
                        'pending_review' => 'Menunggu Review',
                        'revision' => 'Perlu Revisi',
                        'published' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ];
                @endphp
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1.5 rounded-full text-sm font-medium border {{ $statusClasses[$article->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $statusLabels[$article->status] ?? $article->status }}
                    </span>
                    @if(in_array($article->status, ['pending', 'pending_review', 'revision']))
                    <div class="flex gap-2">
                        <button @click="showApproveModal = true"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Setujui
                        </button>
                        <button @click="showRevisionModal = true"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Revisi
                        </button>
                        <button @click="showRejectModal = true"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Tolak
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Article Content Card -->
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <!-- Cover Image -->
                    @if($article->cover_image)
                    <img src="{{ asset('storage/' . $article->cover_image) }}"
                         alt="{{ $article->title }}"
                         class="w-full h-64 md:h-80 object-cover">
                    @endif

                    <div class="p-6">
                        <!-- Title -->
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">{{ $article->title }}</h1>

                        <!-- Meta Info -->
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-6 pb-6 border-b">
                            <div class="flex items-center gap-2">
                                <img src="{{ $article->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($article->user->full_name ?? 'User') }}"
                                     alt="Avatar" class="w-8 h-8 rounded-full">
                                <div>
                                    <span class="font-medium text-gray-700">{{ $article->user->full_name ?? 'Unknown' }}</span>
                                    @if($article->user->class)
                                    <span class="text-gray-400 mx-1">•</span>
                                    <span>{{ $article->user->class }}</span>
                                    @endif
                                </div>
                            </div>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                {{ $article->category->name ?? '-' }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $article->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>

                        <!-- Tags -->
                        @if($article->tags && $article->tags->count() > 0)
                        <div class="flex flex-wrap gap-2 mb-6">
                            @foreach($article->tags as $tag)
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-sm">
                                #{{ $tag->name }}
                            </span>
                            @endforeach
                        </div>
                        @endif

                        <!-- Excerpt -->
                        @if($article->excerpt)
                        <div class="bg-gray-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-6">
                            <p class="text-gray-700 italic">{{ $article->excerpt }}</p>
                        </div>
                        @endif

                        <!-- Content -->
                        <div class="prose prose-lg max-w-none">
                            {!! $article->content !!}
                        </div>
                    </div>
                </div>

                <!-- Review History -->
                @if($article->approvals && $article->approvals->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="px-6 py-4 border-b bg-gray-50">
                        <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Riwayat Review
                        </h2>
                    </div>
                    <div class="divide-y">
                        @foreach($article->approvals->sortByDesc('reviewed_at') as $approval)
                        <div class="p-4">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    @php
                                        $iconClass = match($approval->new_status) {
                                            'published' => 'bg-green-100 text-green-600',
                                            'rejected' => 'bg-red-100 text-red-600',
                                            'revision' => 'bg-blue-100 text-blue-600',
                                            default => 'bg-gray-100 text-gray-600'
                                        };
                                    @endphp
                                    <div class="w-10 h-10 rounded-full {{ $iconClass }} flex items-center justify-center">
                                        @if($approval->new_status === 'published')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        @elseif($approval->new_status === 'rejected')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        @elseif($approval->new_status === 'revision')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="font-medium text-gray-900">
                                            {{ $approval->reviewer->full_name ?? 'System' }}
                                            <span class="font-normal text-gray-500">
                                                @if($approval->new_status === 'published')
                                                    menyetujui artikel
                                                @elseif($approval->new_status === 'rejected')
                                                    menolak artikel
                                                @elseif($approval->new_status === 'revision')
                                                    meminta revisi
                                                @endif
                                            </span>
                                        </p>
                                        <span class="text-sm text-gray-400">
                                            {{ $approval->reviewed_at ? $approval->reviewed_at->format('d M Y, H:i') : ($approval->created_at ? $approval->created_at->format('d M Y, H:i') : '-') }}
                                        </span>
                                    </div>
                                    @if($approval->notes)
                                    <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                                        <p class="text-sm text-gray-600">{{ $approval->notes }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Author Info Card -->
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="px-4 py-3 border-b bg-gray-50">
                        <h3 class="font-semibold text-gray-900">Informasi Penulis</h3>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center gap-4 mb-4">
                            <img src="{{ $article->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($article->user->full_name ?? 'User') }}"
                                 alt="Avatar" class="w-16 h-16 rounded-full">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $article->user->full_name ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-500">{{ $article->user->username ?? '' }}</p>
                                @if($article->user->class)
                                <p class="text-sm text-gray-500">Kelas: {{ $article->user->class }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-900">{{ $article->user->articles()->count() }}</p>
                                <p class="text-sm text-gray-500">Total Artikel</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-green-600">{{ $article->user->articles()->where('status', 'published')->count() }}</p>
                                <p class="text-sm text-gray-500">Disetujui</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Article Stats -->
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="px-4 py-3 border-b bg-gray-50">
                        <h3 class="font-semibold text-gray-900">Statistik Artikel</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Views
                            </span>
                            <span class="font-medium text-gray-900">{{ number_format($article->views_count ?? 0) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                Likes
                            </span>
                            <span class="font-medium text-gray-900">{{ number_format($article->likes_count ?? 0) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Komentar
                            </span>
                            <span class="font-medium text-gray-900">{{ number_format($article->comments_count ?? 0) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Jumlah Kata
                            </span>
                            <span class="font-medium text-gray-900">{{ number_format(str_word_count(strip_tags($article->content))) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                @if(in_array($article->status, ['pending', 'pending_review', 'revision']))
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="px-4 py-3 border-b bg-gray-50">
                        <h3 class="font-semibold text-gray-900">Aksi Cepat</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        <button @click="showApproveModal = true"
                                class="action-card w-full flex items-center gap-3 p-3 rounded-lg border border-green-200 bg-green-50 hover:bg-green-100 text-green-700">
                            <div class="w-10 h-10 rounded-full bg-green-200 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="text-left">
                                <p class="font-medium">Setujui & Publikasikan</p>
                                <p class="text-xs text-green-600">Artikel akan dipublikasikan</p>
                            </div>
                        </button>
                        <button @click="showRevisionModal = true"
                                class="action-card w-full flex items-center gap-3 p-3 rounded-lg border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-700">
                            <div class="w-10 h-10 rounded-full bg-blue-200 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div class="text-left">
                                <p class="font-medium">Minta Revisi</p>
                                <p class="text-xs text-blue-600">Penulis akan merevisi artikel</p>
                            </div>
                        </button>
                        <button @click="showRejectModal = true"
                                class="action-card w-full flex items-center gap-3 p-3 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 text-red-700">
                            <div class="w-10 h-10 rounded-full bg-red-200 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <div class="text-left">
                                <p class="font-medium">Tolak Artikel</p>
                                <p class="text-xs text-red-600">Artikel tidak akan dipublikasikan</p>
                            </div>
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div x-show="showApproveModal" x-cloak @keydown.escape.window="showApproveModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
        <div @click.away="showApproveModal = false"
             class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    Setujui Artikel
                </h3>
            </div>
            <form action="{{ route('approvals.approve', $article->id) }}" method="POST">
                @csrf
                <div class="p-6">
                    <p class="text-gray-600 mb-4">Artikel akan dipublikasikan dan dapat dilihat oleh semua pengguna.</p>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan untuk Penulis (Opsional)</label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="Feedback positif atau catatan untuk penulis..."></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" @click="showApproveModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Setujui & Publikasikan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Revision Modal -->
    <div x-show="showRevisionModal" x-cloak @keydown.escape.window="showRevisionModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
        <div @click.away="showRevisionModal = false"
             class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </span>
                    Minta Revisi
                </h3>
            </div>
            <form action="{{ route('approvals.revision', $article->id) }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <p class="text-gray-600">Penulis akan menerima notifikasi dan diminta untuk merevisi artikel berdasarkan catatan Anda.</p>

                    <!-- Template Buttons -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Template Cepat:</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="document.getElementById('revision_notes').value = 'Mohon perbaiki tata bahasa dan ejaan.'"
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full">Tata Bahasa</button>
                            <button type="button" onclick="document.getElementById('revision_notes').value = 'Tambahkan sumber referensi yang jelas.'"
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full">Referensi</button>
                            <button type="button" onclick="document.getElementById('revision_notes').value = 'Perjelas paragraf pembuka dan penutup.'"
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full">Struktur</button>
                            <button type="button" onclick="document.getElementById('revision_notes').value = 'Gambar cover perlu diganti dengan yang lebih relevan.'"
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full">Cover</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Revisi <span class="text-red-500">*</span></label>
                        <textarea name="revision_notes" id="revision_notes" rows="4" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Jelaskan hal-hal yang perlu diperbaiki oleh penulis..."></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" @click="showRevisionModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Kirim Permintaan Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div x-show="showRejectModal" x-cloak @keydown.escape.window="showRejectModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
        <div @click.away="showRejectModal = false"
             class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </span>
                    Tolak Artikel
                </h3>
            </div>
            <form action="{{ route('approvals.reject', $article->id) }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-red-700 text-sm">Artikel yang ditolak tidak dapat dipublikasikan. Penulis akan menerima notifikasi dengan alasan penolakan.</p>
                    </div>

                    <!-- Template Buttons -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Template Alasan:</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="document.getElementById('rejection_reason').value = 'Artikel tidak sesuai dengan tema/topik yang diperbolehkan.'"
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full">Tidak Sesuai Tema</button>
                            <button type="button" onclick="document.getElementById('rejection_reason').value = 'Artikel mengandung konten yang tidak pantas.'"
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full">Konten Tidak Pantas</button>
                            <button type="button" onclick="document.getElementById('rejection_reason').value = 'Artikel terdeteksi sebagai plagiarisme.'"
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full">Plagiarisme</button>
                            <button type="button" onclick="document.getElementById('rejection_reason').value = 'Kualitas penulisan tidak memenuhi standar minimum.'"
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-full">Kualitas Rendah</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="4" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                  placeholder="Jelaskan alasan penolakan artikel..."></textarea>
                        @error('rejection_reason')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" @click="showRejectModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
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
function reviewManager() {
    return {
        showApproveModal: false,
        showRevisionModal: false,
        showRejectModal: false
    }
}
</script>
@endsection
