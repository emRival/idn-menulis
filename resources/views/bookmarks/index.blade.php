@extends('layouts.app')

@section('title', 'Bookmark Saya - IDN Menulis')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Bookmark Saya</h1>
        <p class="text-gray-600 mt-1">Artikel yang telah Anda simpan</p>
    </div>

    <div id="bookmarks-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($articles as $article)
            <article id="bookmark-{{ $article->id }}" class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
                @if($article->featured_image)
                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}"
                         class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-purple-400"></div>
                @endif
                <div class="p-6">
                    <div class="flex gap-2 mb-2">
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">{{ $article->category->name ?? 'Uncategorized' }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">
                        <a href="{{ route('articles.show', $article) }}" class="hover:text-blue-600">
                            {{ Str::limit($article->title, 60) }}
                        </a>
                    </h2>
                    <p class="text-gray-600 text-sm mb-4">
                        {{ Str::limit($article->excerpt ?? strip_tags($article->content), 100) }}
                    </p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">{{ $article->user->full_name ?? 'Unknown' }}</span>
                        <button type="button"
                                onclick="removeBookmark({{ $article->id }}, '{{ $article->slug }}')"
                                class="text-red-600 hover:text-red-700 text-sm font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Bookmark
                        </button>
                    </div>
                </div>
            </article>
        @empty
            <div id="empty-state" class="col-span-3 py-12 text-center text-gray-500">
                <p>Belum ada artikel yang di-bookmark.</p>
                <a href="{{ route('home') }}" class="text-blue-600 hover:underline mt-2 inline-block">Jelajahi artikel</a>
            </div>
        @endforelse
    </div>

    @if($articles->hasPages())
        <div class="mt-8">
            {{ $articles->links() }}
        </div>
    @endif
</div>

<script>
async function removeBookmark(articleId, articleSlug) {
    if (!confirm('Hapus artikel ini dari bookmark?')) return;

    try {
        const response = await fetch(`/artikel/${articleSlug}/bookmark`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success && !data.bookmarked) {
            // Remove the article card with animation
            const card = document.getElementById(`bookmark-${articleId}`);
            if (card) {
                card.style.transition = 'all 0.3s ease-out';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';

                setTimeout(() => {
                    card.remove();

                    // Check if no more bookmarks
                    const container = document.getElementById('bookmarks-container');
                    if (container.querySelectorAll('article').length === 0) {
                        container.innerHTML = `
                            <div class="col-span-3 py-12 text-center text-gray-500">
                                <p>Belum ada artikel yang di-bookmark.</p>
                                <a href="/" class="text-blue-600 hover:underline mt-2 inline-block">Jelajahi artikel</a>
                            </div>
                        `;
                    }
                }, 300);
            }
        } else {
            alert(data.message || 'Gagal menghapus bookmark.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan. Silakan coba lagi.');
    }
}
</script>
@endsection
