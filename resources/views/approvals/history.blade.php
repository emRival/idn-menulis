@extends('layouts.app')

@section('title', 'Riwayat Persetujuan - ' . $article->title)

@section('styles')
<style>
    .timeline-item {
        position: relative;
    }
    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 40px;
        bottom: -20px;
        width: 2px;
        background-color: #e5e7eb;
    }
    .timeline-dot {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
    }
    .status-published {
        background-color: #dcfce7;
        color: #16a34a;
    }
    .status-rejected {
        background-color: #fee2e2;
        color: #dc2626;
    }
    .status-revision {
        background-color: #fef3c7;
        color: #d97706;
    }
    .status-pending {
        background-color: #dbeafe;
        color: #2563eb;
    }
    .status-draft {
        background-color: #f3f4f6;
        color: #6b7280;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border mb-6 p-6">
            <nav class="flex items-center text-sm text-gray-500 mb-4">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('approvals.pending') }}" class="hover:text-blue-600">Persetujuan</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-700 font-medium">Riwayat</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Riwayat Persetujuan</h1>
                    <p class="text-gray-600 line-clamp-1">{{ $article->title }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('approvals.show', $article->id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Lihat Artikel
                    </a>
                    <a href="{{ route('approvals.pending') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Article Info Card -->
        <div class="bg-white rounded-xl shadow-sm border mb-6 p-6">
            <div class="flex items-start gap-4">
                @if($article->cover_image)
                    <img src="{{ Storage::url($article->cover_image) }}"
                         alt="{{ $article->title }}"
                         class="w-32 h-20 object-cover rounded-lg flex-shrink-0">
                @else
                    <div class="w-32 h-20 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <h2 class="text-lg font-semibold text-gray-900 mb-1">{{ $article->title }}</h2>
                    <p class="text-sm text-gray-500 mb-2">
                        Oleh <span class="font-medium text-gray-700">{{ $article->user->name }}</span>
                        • {{ $article->category->name ?? 'Tanpa Kategori' }}
                    </p>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($article->status === 'published') bg-green-100 text-green-800
                            @elseif($article->status === 'pending') bg-blue-100 text-blue-800
                            @elseif($article->status === 'revision') bg-yellow-100 text-yellow-800
                            @elseif($article->status === 'rejected') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($article->status) }}
                        </span>
                        <span class="text-xs text-gray-500">
                            Dibuat {{ $article->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Timeline Persetujuan
            </h3>

            @if($approvals->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-gray-500">Belum ada riwayat persetujuan untuk artikel ini.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($approvals as $approval)
                        <div class="timeline-item flex gap-4">
                            <!-- Status Dot -->
                            <div class="timeline-dot status-{{ $approval->new_status }}">
                                @if($approval->new_status === 'published')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @elseif($approval->new_status === 'rejected')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                @elseif($approval->new_status === 'revision')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                @elseif($approval->new_status === 'pending')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1 bg-gray-50 rounded-lg p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-900">
                                            @if($approval->new_status === 'published')
                                                Disetujui
                                            @elseif($approval->new_status === 'rejected')
                                                Ditolak
                                            @elseif($approval->new_status === 'revision')
                                                Diminta Revisi
                                            @elseif($approval->new_status === 'pending')
                                                Diajukan untuk Review
                                            @else
                                                Status Diubah
                                            @endif
                                        </span>
                                        <span class="text-xs px-2 py-0.5 rounded-full
                                            @if($approval->new_status === 'published') bg-green-100 text-green-700
                                            @elseif($approval->new_status === 'rejected') bg-red-100 text-red-700
                                            @elseif($approval->new_status === 'revision') bg-yellow-100 text-yellow-700
                                            @elseif($approval->new_status === 'pending') bg-blue-100 text-blue-700
                                            @else bg-gray-100 text-gray-700 @endif">
                                            {{ $approval->previous_status }} → {{ $approval->new_status }}
                                        </span>
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        {{ $approval->reviewed_at ? $approval->reviewed_at->format('d M Y, H:i') : '-' }}
                                    </span>
                                </div>

                                @if($approval->reviewer)
                                    <p class="text-sm text-gray-600 mb-2">
                                        Oleh <span class="font-medium">{{ $approval->reviewer->name }}</span>
                                        <span class="text-gray-400">({{ ucfirst($approval->reviewer->role) }})</span>
                                    </p>
                                @endif

                                @if($approval->notes)
                                    <div class="mt-3 p-3 bg-white border border-gray-200 rounded-lg">
                                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $approval->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
