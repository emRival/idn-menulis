@extends('layouts.app')

@section('title', 'Kelola Komentar - Admin IDN Menulis')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kelola Komentar</h1>
                <p class="text-gray-600 mt-1">Semua komentar dari semua artikel</p>
            </div>
            <div class="flex gap-3 text-sm">
                <span class="px-3 py-1.5 rounded-full bg-green-100 text-green-700 font-medium">{{ $stats['approved'] }}
                    Disetujui</span>
                <span class="px-3 py-1.5 rounded-full bg-amber-100 text-amber-700 font-medium">{{ $stats['pending'] }}
                    Menunggu</span>
                <span class="px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 font-medium">{{ $stats['total'] }}
                    Total</span>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
            <form method="GET" action="{{ route('admin.comments') }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari komentar, pengguna, atau artikel..."
                    class="flex-1 min-w-[200px] px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">

                <select name="status"
                    class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="">Semua Status</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                </select>

                <button type="submit"
                    class="px-5 py-2 bg-teal-600 text-white rounded-lg text-sm font-medium hover:bg-teal-700 transition-colors">
                    Filter
                </button>

                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.comments') }}"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 text-sm">Reset</a>
                @endif
            </form>
        </div>

        <!-- Comments List -->
        <div class="space-y-4">
            @forelse($comments as $comment)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2">
                                <div
                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-teal-400 to-blue-500 flex items-center justify-center flex-shrink-0">
                                    <span
                                        class="text-white text-xs font-medium">{{ substr($comment->user->full_name ?? 'U', 0, 1) }}</span>
                                </div>
                                <div>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $comment->user->full_name ?? 'Unknown' }}</span>
                                    <span class="text-xs text-gray-400 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                @if($comment->is_approved)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        Disetujui
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                        Menunggu
                                    </span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-700 mb-2 line-clamp-3">{{ $comment->content }}</p>

                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <a href="{{ route('articles.show', $comment->article->slug ?? '#') }}"
                                    class="text-teal-600 hover:text-teal-700" target="_blank">
                                    {{ Str::limit($comment->article->title ?? 'Artikel tidak ditemukan', 60) }}
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if(!$comment->is_approved)
                                <button onclick="approveComment({{ $comment->id }})"
                                    class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-medium hover:bg-green-100 transition-colors">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Setujui
                                </button>
                            @endif
                            <button onclick="deleteComment({{ $comment->id }})"
                                class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-xs font-medium hover:bg-red-100 transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="text-gray-500">Tidak ada komentar ditemukan.</p>
                </div>
            @endforelse
        </div>

        @if($comments->hasPages())
            <div class="mt-6">
                {{ $comments->links() }}
            </div>
        @endif
    </div>

    <script>
        function approveComment(commentId) {
            if (!confirm('Setujui komentar ini?')) return;

            fetch(`/komentar/${commentId}/setujui`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            }).then(res => res.json()).then(data => {
                if (data.success) location.reload();
                else alert(data.message || 'Gagal menyetujui komentar.');
            }).catch(() => alert('Terjadi kesalahan.'));
        }

        function deleteComment(commentId) {
            if (!confirm('Hapus komentar ini?')) return;

            fetch(`/komentar/${commentId}/hapus-admin`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            }).then(res => res.json()).then(data => {
                if (data.success) location.reload();
                else alert(data.message || 'Gagal menghapus komentar.');
            }).catch(() => alert('Terjadi kesalahan.'));
        }
    </script>
@endsection