@extends('layouts.app')

@section('title', 'IDN Menulis - Platform Literasi Digital Indonesia')
@section('meta_title', 'IDN Menulis - Platform Literasi Digital Indonesia')
@section('meta_description', 'Platform menulis dan berbagi karya tulis terbaik di Indonesia. Tulis, baca, dan bagikan karya tulismu bersama IDN Menulis.')
@section('meta_keywords', 'menulis, blog, artikel, cerita, indonesia, penulis, literasi digital, karya tulis')
@section('og_image', url('images/og-default.jpg'))
@section('content')

    <!-- Hero Section -->
    <section class="relative min-h-[90vh] flex items-center overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900"></div>
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5" />
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#grid)" />
            </svg>
        </div>

        <!-- Floating Elements -->
        <div class="absolute top-20 left-10 w-72 h-72 bg-accent-500/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-primary-400/20 rounded-full blur-3xl animate-pulse delay-1000">
        </div>

        <!-- Content -->
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="text-white">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium mb-6">
                        <span class="w-2 h-2 bg-accent-400 rounded-full animate-pulse"></span>
                        Platform Literasi Digital #1 Indonesia
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold font-display leading-tight mb-6">
                        Wadah Karya Tulis
                        <span class="block text-accent-400">Siswa Inspiratif</span>
                    </h1>

                    <p class="text-lg sm:text-xl text-primary-100 mb-8 leading-relaxed max-w-xl">
                        Berbagi ide, ilmu, dan pengalaman melalui tulisan. Jadilah bagian dari komunitas penulis muda
                        Indonesia yang kreatif dan inspiratif.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('articles.index') }}"
                            class="inline-flex items-center gap-2 px-8 py-4 bg-white text-primary-700 rounded-2xl font-semibold shadow-xl shadow-black/10 hover:shadow-2xl hover:shadow-black/20 hover:-translate-y-1 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Baca Artikel
                        </a>
                        <a href="{{ route('articles.create') }}"
                            class="inline-flex items-center gap-2 px-8 py-4 bg-accent-500 text-white rounded-2xl font-semibold shadow-xl shadow-accent-500/30 hover:shadow-2xl hover:shadow-accent-500/40 hover:bg-accent-600 hover:-translate-y-1 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Tulis Sekarang
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6 mt-12 pt-8 border-t border-white/20">
                        <div>
                            <p class="text-3xl sm:text-4xl font-bold">
                                {{ \App\Models\Article::where('status', 'published')->count() }}+</p>
                            <p class="text-primary-200 text-sm sm:text-base">Artikel</p>
                        </div>
                        <div>
                            <p class="text-3xl sm:text-4xl font-bold">{{ \App\Models\User::count() }}+</p>
                            <p class="text-primary-200 text-sm sm:text-base">Penulis</p>
                        </div>
                        <div>
                            <p class="text-3xl sm:text-4xl font-bold">{{ \App\Models\Category::count() }}</p>
                            <p class="text-primary-200 text-sm sm:text-base">Kategori</p>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Hero Image/Illustration -->
                <div class="hidden lg:block relative">
                    <div class="relative z-10">
                        <!-- Decorative Cards Stack -->
                        <div
                            class="absolute -top-4 -left-4 w-64 h-40 bg-white/10 backdrop-blur-sm rounded-2xl transform rotate-6">
                        </div>
                        <div
                            class="absolute -bottom-4 -right-4 w-64 h-40 bg-accent-500/20 backdrop-blur-sm rounded-2xl transform -rotate-6">
                        </div>

                        <!-- Main Card -->
                        <div
                            class="relative bg-white rounded-3xl shadow-2xl p-6 transform hover:scale-105 transition-transform duration-500">
                            <div class="flex items-center gap-3 mb-4">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center text-white text-xl">
                                    📝</div>
                                <div>
                                    <p class="font-semibold text-gray-800">Artikel Terbaru</p>
                                    <p class="text-sm text-gray-500">Dari komunitas penulis</p>
                                </div>
                            </div>

                            @if($latest->first())
                                <div class="space-y-3">
                                    <div
                                        class="h-32 bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl overflow-hidden">
                                        @if($latest->first()->featured_image)
                                            <img src="{{ Storage::url($latest->first()->featured_image) }}"
                                                class="w-full h-full object-cover" alt="">
                                        @endif
                                    </div>
                                    <h3 class="font-semibold text-gray-800 line-clamp-2">
                                        {{ $latest->first()->title ?? 'Judul Artikel Terbaru' }}</h3>
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $latest->first()->user->avatar ? Storage::url($latest->first()->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($latest->first()->user->full_name) . '&background=14b8a6&color=fff' }}"
                                            class="w-6 h-6 rounded-full object-cover" alt="">
                                        <span
                                            class="text-sm text-gray-500">{{ $latest->first()->user->full_name ?? 'Penulis' }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
            <a href="#featured" class="text-white/60 hover:text-white transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </a>
        </div>
    </section>

    <!-- Featured Articles Slider -->
    @if($featured->count() > 0)
        <section id="featured" class="py-16 lg:py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
                    <div>
                        <span
                            class="inline-block px-3 py-1 bg-accent-100 text-accent-600 text-sm font-medium rounded-full mb-3">Pilihan
                            Editor</span>
                        <h2 class="text-3xl lg:text-4xl font-bold font-display text-gray-900">Artikel Unggulan</h2>
                    </div>
                    <a href="{{ route('articles.index') }}"
                        class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 font-medium group">
                        Lihat Semua
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>

                <!-- Swiper Slider -->
                <div class="swiper featuredSwiper">
                    <div class="swiper-wrapper pb-12">
                        @foreach($featured as $article)
                            <div class="swiper-slide">
                                <article
                                    class="group relative bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 card-hover">
                                    <!-- Image -->
                                    <div class="relative h-64 overflow-hidden">
                                        @if($article->featured_image)
                                            <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}"
                                                class="w-full h-full object-cover img-zoom">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-primary-400 to-primary-600"></div>
                                        @endif

                                        <!-- Overlay -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-transparent">
                                        </div>

                                        <!-- Category Badge -->
                                        <div class="absolute top-4 left-4">
                                            <a href="{{ route('categories.show', $article->category->slug) }}"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-white/90 backdrop-blur-sm rounded-full text-sm font-medium text-gray-800 hover:bg-white transition-colors">
                                                <span>{{ $article->category->icon ?? '📁' }}</span>
                                                {{ $article->category->name }}
                                            </a>
                                        </div>

                                        <!-- Reading Time -->
                                        <div class="absolute top-4 right-4">
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-black/50 backdrop-blur-sm rounded-full text-sm text-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $article->reading_time ?? 5 }} min
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-6">
                                        <h3
                                            class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-primary-600 transition-colors">
                                            <a href="{{ route('articles.show', $article->slug) }}">
                                                {{ $article->title }}
                                            </a>
                                        </h3>

                                        <p class="text-gray-600 mb-4 line-clamp-2">
                                            {{ Str::limit($article->excerpt ?? strip_tags($article->content), 120) }}
                                        </p>

                                        <!-- Author & Meta -->
                                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ $article->user->avatar ? Storage::url($article->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($article->user->full_name) . '&background=14b8a6&color=fff' }}"
                                                    alt="{{ $article->user->full_name }}" class="w-10 h-10 rounded-xl object-cover">
                                                <div>
                                                    <p class="font-medium text-gray-900 text-sm">{{ $article->user->full_name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $article->published_at->format('d M Y') }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-3 text-sm text-gray-500">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    {{ number_format($article->views_count) }}
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                                    </svg>
                                                    {{ $article->liked_by_count }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section>
    @endif

    <!-- Categories Section -->
    <section class="py-16 lg:py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span
                    class="inline-block px-3 py-1 bg-primary-100 text-primary-600 text-sm font-medium rounded-full mb-3">Eksplorasi</span>
                <h2 class="text-3xl lg:text-4xl font-bold font-display text-gray-900 mb-4">Jelajahi Kategori</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Temukan artikel sesuai minat dan kebutuhanmu</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('categories.show', $category->slug) }}"
                        class="group relative bg-white rounded-2xl p-6 text-center shadow-sm hover:shadow-xl transition-all duration-300 card-hover overflow-hidden">
                        <!-- Background Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            style="background: linear-gradient(135deg, {{ $category->color ?? '#14b8a6' }}15, {{ $category->color ?? '#14b8a6' }}05)">
                        </div>

                        <!-- Icon -->
                        <div class="relative w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-gray-100 to-gray-50 rounded-2xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform duration-300"
                            style="box-shadow: 0 4px 20px {{ $category->color ?? '#14b8a6' }}20">
                            {{ $category->icon ?? '📁' }}
                        </div>

                        <!-- Name -->
                        <h4 class="relative font-semibold text-gray-900 mb-1 group-hover:text-primary-600 transition-colors">
                            {{ $category->name }}</h4>

                        <!-- Count -->
                        <p class="relative text-sm text-gray-500">
                            {{ $category->articles_count }} artikel
                        </p>

                        <!-- Arrow -->
                        <div
                            class="absolute bottom-4 right-4 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Trending Articles Section -->
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
                <div>
                    <span class="inline-block px-3 py-1 bg-red-100 text-red-600 text-sm font-medium rounded-full mb-3">🔥
                        Trending</span>
                    <h2 class="text-3xl lg:text-4xl font-bold font-display text-gray-900">Sedang Populer</h2>
                </div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                @foreach($popular->take(6) as $index => $article)
                    <article
                        class="group flex gap-4 p-4 bg-white rounded-2xl border border-gray-100 hover:border-primary-200 hover:shadow-lg transition-all duration-300">
                        <!-- Number -->
                        <div
                            class="flex-shrink-0 w-12 h-12 bg-gradient-to-br {{ $index < 3 ? 'from-accent-500 to-accent-600 text-white' : 'from-gray-100 to-gray-50 text-gray-400' }} rounded-xl flex items-center justify-center font-bold text-xl">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('categories.show', $article->category->slug) }}"
                                class="inline-block text-xs font-medium text-primary-600 mb-1">{{ $article->category->name }}</a>
                            <h3
                                class="font-semibold text-gray-900 line-clamp-2 group-hover:text-primary-600 transition-colors mb-2">
                                <a href="{{ route('articles.show', $article->slug) }}">{{ $article->title }}</a>
                            </h3>
                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                <span>{{ $article->user->full_name }}</span>
                                <span>•</span>
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
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Latest Articles with Sidebar -->
    <section class="py-16 lg:py-24 bg-slate-50" id="latest-articles">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
                <div>
                    <span
                        class="inline-block px-3 py-1 bg-primary-100 text-primary-600 text-sm font-medium rounded-full mb-3">Terbaru</span>
                    <h2 class="text-3xl lg:text-4xl font-bold font-display text-gray-900">Artikel Terbaru</h2>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <div class="grid sm:grid-cols-2 gap-6">
                        @foreach($latest as $article)
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
                                            class="inline-block px-2.5 py-1 bg-white/90 backdrop-blur-sm rounded-lg text-xs font-medium"
                                            style="color: {{ $article->category->color ?? '#14b8a6' }}">
                                            {{ $article->category->name }}
                                        </a>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-5">
                                    <h3
                                        class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-primary-600 transition-colors">
                                        <a href="{{ route('articles.show', $article->slug) }}">{{ $article->title }}</a>
                                    </h3>

                                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                        {{ Str::limit($article->excerpt ?? strip_tags($article->content), 100) }}
                                    </p>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $article->user->avatar ? Storage::url($article->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($article->user->full_name) . '&background=14b8a6&color=fff&size=32' }}"
                                                alt="" class="w-7 h-7 rounded-lg object-cover">
                                            <span class="text-sm text-gray-600">{{ $article->user->full_name }}</span>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $article->published_at->format('d M') }}</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-10">
                        {{ $latest->links() }}
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="space-y-8">
                    <!-- Search -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-gray-900 mb-4">Cari Artikel</h3>
                        <form action="{{ route('articles.search') }}" method="GET">
                            <div class="relative">
                                <input type="text" name="q" placeholder="Kata kunci..."
                                    class="w-full py-3 pl-4 pr-12 rounded-xl border border-gray-200 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all">
                                <button type="submit"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 p-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Popular Tags -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-gray-900 mb-4">Tag Populer</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                                <a href="{{ route('tags.show', $tag->slug) }}"
                                    class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-primary-500 hover:text-white transition-colors">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Top Writers -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-gray-900 mb-4">Penulis Aktif</h3>
                        <div class="space-y-4">

                            @foreach($topWriters as $writer)
                                <div class="flex items-center gap-3">
                                    <img src="{{ $writer->avatar ? Storage::url($writer->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($writer->full_name) . '&background=14b8a6&color=fff' }}"
                                        alt="{{ $writer->full_name }}" class="w-10 h-10 rounded-xl object-cover">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 text-sm truncate">{{ $writer->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $writer->articles_count }} artikel</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl p-6 text-white">
                        <h3 class="font-bold text-xl mb-2">Mulai Menulis</h3>
                        <p class="text-primary-100 text-sm mb-4">Bergabunglah dengan ribuan penulis lainnya dan bagikan
                            ceritamu.</p>
                        @auth
                            <a href="{{ route('articles.create') }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-primary-600 rounded-xl font-medium hover:bg-primary-50 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Buat Artikel
                            </a>
                        @else
                            {{-- Hidden: Daftar Sekarang button
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-primary-600 rounded-xl font-medium hover:bg-primary-50 transition-colors">
                                Daftar Sekarang
                            </a>
                            --}}
                        @endauth
                    </div>
                </aside>
            </div>
        </div>
    </section>



@endsection

@section('scripts')
    <script>
        // Featured Articles Swiper
        new Swiper('.featuredSwiper', {
            slidesPerView: 1,
            spaceBetween: 24,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
            },
        });
    </script>
@endsection