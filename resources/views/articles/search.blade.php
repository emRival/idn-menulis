@extends('layouts.app')

@section('title', ($query ? "Hasil Pencarian: {$query}" : 'Cari Artikel') . ' - IDN Menulis')
@section('meta_title', ($query ? "Hasil Pencarian: {$query}" : 'Cari Artikel') . ' - IDN Menulis')
@section('meta_description', $query ? "Hasil pencarian untuk \"{$query}\" di IDN Menulis." : 'Cari artikel di IDN Menulis.')
@section('content')
    <!-- Hero Header -->
    <section class="bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl lg:text-5xl font-bold font-display text-white mb-4">
                @if($query)
                    Hasil Pencarian
                @else
                    Cari Artikel
                @endif
            </h1>
            @if($query)
                <p class="text-primary-100 text-lg max-w-2xl mx-auto">
                    Menampilkan hasil untuk "<span class="font-semibold text-white">{{ $query }}</span>"
                </p>
            @else
                <p class="text-primary-100 text-lg max-w-2xl mx-auto">Temukan artikel yang kamu cari</p>
            @endif
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto mb-10">
            <form action="{{ route('articles.search') }}" method="GET" class="flex gap-3">
                <div class="relative flex-1">
                    <input type="text" name="q" value="{{ $query }}" placeholder="Cari artikel..."
                        class="w-full py-3.5 pl-12 pr-4 rounded-2xl border border-gray-200 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 text-gray-900 bg-white shadow-sm">
                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button type="submit"
                    class="px-6 py-3.5 bg-primary-500 text-white rounded-2xl font-medium hover:bg-primary-600 transition-colors shadow-sm">
                    Cari
                </button>
            </form>
        </div>

        @if($query)
            <!-- Results Count -->
            <div class="flex items-center justify-between mb-6">
                <p class="text-gray-600">
                    Ditemukan <span class="font-semibold text-gray-900">{{ $articles->total() }}</span> artikel
                </p>

                @if($categories->count() > 0)
                    <div class="hidden sm:flex items-center gap-2 flex-wrap">
                        <a href="{{ route('articles.search', ['q' => $query]) }}"
                            class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ !request('category') ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Semua
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('articles.search', ['q' => $query, 'category' => $cat->id]) }}"
                                class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ request('category') == $cat->id ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                {{ $cat->icon ?? '📁' }} {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Articles Grid -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($articles as $article)
                    <article
                        class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 card-hover">
                        <!-- Image -->
                        <div class="relative h-48 overflow-hidden">
                            @if($article->featured_image)
                                <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}"
                                    class="w-full h-full object-cover img-zoom">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary-400 to-primary-600"></div>
                            @endif

                            <div class="absolute top-3 left-3">
                                <a href="{{ route('categories.show', $article->category->slug) }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-white/90 backdrop-blur-sm rounded-lg text-xs font-medium text-gray-800">
                                    <span>{{ $article->category->icon ?? '📁' }}</span>
                                    {{ $article->category->name }}
                                </a>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{ number_format($article->views_count) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full py-16 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ditemukan</h3>
                        <p class="text-gray-500 mb-6">Tidak ada artikel yang cocok dengan kata kunci "<strong>{{ $query }}</strong>"
                        </p>
                        <a href="{{ route('articles.index') }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-primary-500 text-white rounded-xl font-medium hover:bg-primary-600 transition-colors">
                            Lihat Semua Artikel
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($articles->hasPages())
                <div class="mt-10">
                    {{ $articles->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection