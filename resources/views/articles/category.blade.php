@extends('layouts.app')

@section('title', $category->name . ' - IDN Menulis')

@section('content')
<!-- Hero Header -->
<section class="bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl mb-4">
            <span class="text-3xl">{{ $category->icon ?? '📁' }}</span>
        </div>
        <h1 class="text-4xl lg:text-5xl font-bold font-display text-white mb-4">{{ $category->name }}</h1>
        @if($category->description)
        <p class="text-primary-100 text-lg max-w-2xl mx-auto">{{ $category->description }}</p>
        @else
        <p class="text-primary-100 text-lg max-w-2xl mx-auto">Temukan artikel menarik seputar {{ $category->name }}</p>
        @endif
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="lg:grid lg:grid-cols-4 lg:gap-8">

        <!-- Sidebar -->
        <aside class="hidden lg:block">
            <!-- Search -->
            <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
                <h3 class="font-bold text-gray-900 mb-4">Cari Artikel</h3>
                <form action="{{ route('articles.search') }}" method="GET">
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Kata kunci..."
                               class="w-full py-3 pl-4 pr-12 rounded-xl border border-gray-200 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Categories -->
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-4">Kategori Lainnya</h3>
                <div class="space-y-2">
                    @foreach($categories as $cat)
                    <a href="{{ route('categories.show', $cat->slug) }}"
                       class="flex items-center justify-between p-3 rounded-xl transition-colors group {{ $cat->id === $category->id ? 'bg-primary-50 ring-1 ring-primary-200' : 'hover:bg-primary-50' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">{{ $cat->icon ?? '📁' }}</span>
                            <span class="font-medium {{ $cat->id === $category->id ? 'text-primary-600' : 'text-gray-700 group-hover:text-primary-600' }}">{{ $cat->name }}</span>
                        </div>
                        <span class="text-sm {{ $cat->id === $category->id ? 'text-primary-500' : 'text-gray-400' }}">{{ $cat->articles_count }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <!-- Mobile Search -->
            <div class="lg:hidden bg-white rounded-2xl p-4 shadow-sm mb-6">
                <form action="{{ route('articles.search') }}" method="GET" class="flex gap-2">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari artikel..."
                           class="flex-1 py-2.5 px-4 rounded-xl border border-gray-200 focus:outline-none focus:border-primary-500">
                    <button type="submit" class="px-4 py-2.5 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Results Count -->
            <div class="flex items-center justify-between mb-6">
                <p class="text-gray-600">
                    Menampilkan <span class="font-semibold text-gray-900">{{ $articles->count() }}</span> dari
                    <span class="font-semibold text-gray-900">{{ $articles->total() }}</span> artikel
                </p>

                <!-- Breadcrumb -->
                <nav class="hidden sm:flex items-center gap-2 text-sm">
                    <a href="{{ route('home') }}" class="text-gray-500 hover:text-primary-500 transition-colors">Beranda</a>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-gray-900 font-medium">{{ $category->name }}</span>
                </nav>
            </div>

            <!-- Articles Grid -->
            <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($articles as $article)
                <article class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 card-hover">
                    <!-- Image -->
                    <div class="relative h-48 overflow-hidden">
                        @if($article->featured_image)
                        <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}"
                             class="w-full h-full object-cover img-zoom">
                        @else
                        <div class="w-full h-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center">
                            <span class="text-6xl opacity-50">{{ $category->icon ?? '📁' }}</span>
                        </div>
                        @endif

                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-white/90 backdrop-blur-sm rounded-lg text-xs font-medium text-gray-800">
                                <span>{{ $category->icon ?? '📁' }}</span>
                                {{ $category->name }}
                            </span>
                        </div>

                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-black/50 backdrop-blur-sm rounded-lg text-xs text-white">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $article->reading_time ?? 5 }} min
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-5">
                        <h2 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-primary-600 transition-colors">
                            <a href="{{ route('articles.show', $article->slug) }}">{{ $article->title }}</a>
                        </h2>

                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                            {{ Str::limit($article->excerpt ?? strip_tags($article->content), 100) }}
                        </p>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-2">
                                <img src="{{ $article->user->avatar ? Storage::url($article->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($article->user->full_name) . '&background=14b8a6&color=fff&size=32' }}"
                                     alt="{{ $article->user->full_name }}" class="w-7 h-7 rounded-lg object-cover">
                                <span class="text-sm text-gray-600">{{ $article->user->full_name }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ number_format($article->views_count) }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    {{ $article->likedBy()->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </article>
                @empty
                <div class="col-span-full py-16 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="text-4xl">{{ $category->icon ?? '📁' }}</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Artikel</h3>
                    <p class="text-gray-500 mb-6">Belum ada artikel dalam kategori {{ $category->name }}</p>
                    @auth
                    <a href="{{ route('articles.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-500 text-white rounded-xl font-medium hover:bg-primary-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Tulis Artikel
                    </a>
                    @else
                    <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-500 text-white rounded-xl font-medium hover:bg-primary-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Lihat Semua Artikel
                    </a>
                    @endauth
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($articles->hasPages())
            <div class="mt-10">
                {{ $articles->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
