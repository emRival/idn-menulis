@extends('layouts.app')

@section('title', $article->title . ' - IDN Menulis')
@section('description', Str::limit($article->excerpt ?? strip_tags($article->content), 160))

@section('styles')
    <style>
        /* Reading Progress Bar */
        .reading-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 4px;
            background: linear-gradient(90deg, #14b8a6, #0d9488);
            z-index: 100;
            transition: width 0.1s ease-out;
        }

        /* Article Content Typography */
        .article-content {
            font-size: 1.125rem;
            line-height: 1.85;
            color: #374151;
        }

        .article-content.text-sm-size {
            font-size: 1rem;
        }

        .article-content.text-lg-size {
            font-size: 1.25rem;
        }

        .article-content.text-xl-size {
            font-size: 1.375rem;
        }

        .article-content p {
            margin-bottom: 1.5rem;
        }

        .article-content h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            color: #111827;
            font-family: 'Poppins', sans-serif;
        }

        .article-content h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-top: 2rem;
            margin-bottom: 0.75rem;
            color: #1f2937;
            font-family: 'Poppins', sans-serif;
        }

        .article-content a {
            color: #0d9488;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .article-content a:hover {
            color: #14b8a6;
        }

        .article-content blockquote {
            border-left: 4px solid #14b8a6;
            padding-left: 1.5rem;
            margin: 2rem 0;
            font-style: italic;
            color: #4b5563;
            background: #f0fdfa;
            padding: 1.5rem;
            border-radius: 0 1rem 1rem 0;
        }

        .article-content ul,
        .article-content ol {
            margin: 1.5rem 0;
            padding-left: 1.5rem;
        }

        .article-content li {
            margin-bottom: 0.5rem;
        }

        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 1rem;
            margin: 2rem auto;
            display: block;
        }

        .article-content pre {
            background: #1f2937;
            color: #e5e7eb;
            padding: 1.5rem;
            border-radius: 1rem;
            overflow-x: auto;
            margin: 2rem 0;
            font-size: 0.9rem;
        }

        .article-content code {
            background: #f3f4f6;
            padding: 0.2rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.9em;
        }

        .article-content pre code {
            background: transparent;
            padding: 0;
        }

        /* Floating Action Bar */
        .floating-bar {
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 40;
        }

        /* Share dropdown animation */
        .share-dropdown {
            transform-origin: bottom center;
        }
    </style>
@endsection

@section('content')
    <!-- Reading Progress Bar -->
    <div class="reading-progress" id="reading-progress"></div>

    <article x-data="articleActions()" class="relative">
        <!-- Article Header -->
        <header class="bg-white pt-8 pb-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Main Header Content -->
                    <div class="flex-1">
                        <!-- Category -->
                        <a href="{{ route('categories.show', $article->category->slug) }}"
                            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-medium mb-6 transition-colors"
                            style="background-color: {{ $article->category->color }}15; color: {{ $article->category->color }}">
                            <span>{{ $article->category->icon ?? '📁' }}</span>
                            {{ $article->category->name }}
                        </a>

                        <!-- Title -->
                        <h1
                            class="text-3xl sm:text-4xl lg:text-5xl font-bold font-display text-gray-900 leading-tight mb-6">
                            {{ $article->title }}
                        </h1>

                        <!-- Excerpt -->
                        @if($article->excerpt)
                            <p class="text-xl text-gray-600 leading-relaxed mb-8">
                                {{ $article->excerpt }}
                            </p>
                        @endif

                        <!-- Author & Meta -->
                        <div class="flex flex-wrap items-center gap-6 pb-8 border-b border-gray-100">
                            <!-- Author -->
                            <div class="flex items-center gap-3">
                                <img src="{{ $article->user->avatar ? Storage::url($article->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($article->user->full_name) . '&background=14b8a6&color=fff' }}"
                                    alt="{{ $article->user->full_name }}"
                                    class="w-12 h-12 rounded-xl object-cover ring-2 ring-gray-100">
                                <div>
                                    <a href="#author-box"
                                        class="font-semibold text-gray-900 hover:text-primary-600 transition-colors">
                                        {{ $article->user->full_name }}
                                    </a>
                                    <p class="text-sm text-gray-500">
                                        {{ ucfirst($article->user->role) }}
                                    </p>
                                </div>
                            </div>

                            <div class="hidden sm:block w-px h-8 bg-gray-200"></div>

                            <!-- Date -->
                            <div class="flex items-center gap-2 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $article->published_at?->format('d M Y') }}</span>
                            </div>

                            <!-- Reading Time -->
                            <div class="flex items-center gap-2 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $article->reading_time ?? 5 }} menit baca</span>
                            </div>
                        </div>

                        <!-- Stats Bar -->
                        <div class="flex flex-wrap items-center gap-6 py-4 text-sm text-gray-500">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                {{ number_format($article->views_count) }} views
                            </span>
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                </svg>
                                <span x-text="likesCount"></span> likes
                            </span>
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                {{ $commentsCount }} komentar
                            </span>

                            <!-- Text Size Control -->
                            <div class="ml-auto flex items-center gap-2">
                                <span class="text-gray-400 text-xs">Ukuran:</span>
                                <button @click="setTextSize('sm')"
                                    :class="textSize === 'sm' ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-600'"
                                    class="w-8 h-8 rounded-lg text-xs font-medium transition-colors">A</button>
                                <button @click="setTextSize('md')"
                                    :class="textSize === 'md' ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-600'"
                                    class="w-8 h-8 rounded-lg text-sm font-medium transition-colors">A</button>
                                <button @click="setTextSize('lg')"
                                    :class="textSize === 'lg' ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-600'"
                                    class="w-8 h-8 rounded-lg text-base font-medium transition-colors">A</button>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Spacer -->
                    <aside class="hidden lg:block lg:w-80 xl:w-96 flex-shrink-0"></aside>
                </div>
            </div>
        </header>

        <!-- Cover Image + Related Articles Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 mb-12">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Cover Image -->
                <div class="flex-1">
                    @if($article->featured_image)
                        <div class="relative aspect-video rounded-2xl overflow-hidden shadow-2xl">
                            <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}"
                                class="w-full h-full object-cover" loading="lazy">
                        </div>
                    @else
                        <div
                            class="relative aspect-video rounded-2xl overflow-hidden shadow-lg bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Right: Related Articles -->
                @if($related->count() > 0)
                    <aside class="lg:w-80 xl:w-96 flex-shrink-0">
                        <div class="h-full">
                            <h3 class="text-lg font-bold font-display text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                                Artikel Terkait
                            </h3>
                            <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                                @foreach($related->take(6) as $relatedArticle)
                                    <a href="{{ route('articles.show', $relatedArticle->slug) }}"
                                        class="group flex gap-3 p-3 bg-white rounded-xl border border-gray-100 hover:border-primary-200 hover:shadow-md transition-all duration-300">
                                        @if($relatedArticle->featured_image)
                                            <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
                                                <img src="{{ Storage::url($relatedArticle->featured_image) }}"
                                                    alt="{{ $relatedArticle->title }}"
                                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                            </div>
                                        @else
                                            <div
                                                class="w-16 h-16 rounded-lg bg-gradient-to-br from-primary-400 to-primary-600 flex-shrink-0 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white/80" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <span
                                                class="text-xs font-medium text-primary-600 mb-0.5 block">{{ $relatedArticle->category->name }}</span>
                                            <h4
                                                class="font-semibold text-gray-900 text-sm line-clamp-2 group-hover:text-primary-600 transition-colors leading-snug">
                                                {{ $relatedArticle->title }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                                <span>{{ $relatedArticle->published_at?->format('d M') ?? '-' }}</span>
                                                <span>•</span>
                                                <span>{{ $relatedArticle->reading_time ?? 5 }} min</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </aside>
                @endif
            </div>
        </div>

        <!-- Article Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Main Content -->
                <div class="flex-1">
                    <div class="article-content"
                        :class="{ 'text-sm-size': textSize === 'sm', 'text-lg-size': textSize === 'lg', 'text-xl-size': textSize === 'xl' }"
                        id="article-content">
                        {!! $article->content !!}
                    </div>

                    <!-- Tags -->
                    @if($article->tags->count() > 0)
                        <div class="mt-12 pt-8 border-t border-gray-100">
                            <div class="flex flex-wrap gap-2">
                                @foreach($article->tags as $tag)
                                    <a href="{{ route('tags.show', $tag->slug) }}"
                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-primary-500 hover:text-white transition-colors">
                                        #{{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Share Section -->
                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-500 mb-4">Bagikan artikel ini</h3>
                        <div class="flex flex-wrap gap-3">
                            <!-- WhatsApp -->
                            <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . url()->current()) }}"
                                target="_blank"
                                class="flex items-center gap-2 px-4 py-2.5 bg-green-500 text-white rounded-xl font-medium hover:bg-green-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                </svg>
                                WhatsApp
                            </a>

                            <!-- Twitter/X -->
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(url()->current()) }}"
                                target="_blank"
                                class="flex items-center gap-2 px-4 py-2.5 bg-black text-white rounded-xl font-medium hover:bg-gray-800 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                </svg>
                                Twitter
                            </a>

                            <!-- Facebook -->
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                target="_blank"
                                class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />
                                </svg>
                                Facebook
                            </a>

                            <!-- Copy Link -->
                            <button @click="copyLink()"
                                class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                                <span x-text="linkCopied ? 'Tersalin!' : 'Salin Link'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Author Box -->
                    <div id="author-box"
                        class="mt-12 p-8 bg-gradient-to-br from-primary-50 to-white rounded-2xl border border-primary-100">
                        <div class="flex flex-col sm:flex-row gap-6">
                            <img src="{{ $article->user->avatar ? Storage::url($article->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($article->user->full_name) . '&background=14b8a6&color=fff&size=128' }}"
                                alt="{{ $article->user->full_name }}"
                                class="w-24 h-24 rounded-2xl object-cover ring-4 ring-white shadow-lg">
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                    <h3 class="text-xl font-bold text-gray-900">{{ $article->user->full_name }}</h3>
                                    <span
                                        class="px-2 py-0.5 text-xs font-medium rounded-full
                                            {{ $article->user->role === 'admin' ? 'bg-red-100 text-red-700' : ($article->user->role === 'guru' ? 'bg-blue-100 text-blue-700' : 'bg-primary-100 text-primary-700') }}">
                                        {{ ucfirst($article->user->role) }}
                                    </span>
                                </div>
                                <p class="text-gray-600 mb-4">{{ $article->user->bio ?? 'Penulis di IDN Menulis' }}</p>
                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        {{ $article->user->articles()->where('status', 'published')->count() }} artikel
                                    </span>
                                    @if($article->user->school)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            {{ $article->user->school }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Spacer (for alignment with cover image section) -->
                <aside class="hidden lg:block lg:w-80 xl:w-96 flex-shrink-0"></aside>
            </div>
        </div>

        <!-- Floating Action Bar -->
        <div class="floating-bar" x-show="showFloatingBar" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4">
            <div
                class="flex items-center gap-2 px-4 py-3 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-gray-100">
                @auth
                    <!-- Like Button -->
                    <button @click="toggleLike()"
                        :class="isLiked ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-red-100'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-medium transition-all duration-300">
                        <svg class="w-5 h-5" :class="isLiked ? 'scale-110' : ''" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                        </svg>
                        <span x-text="likesCount"></span>
                    </button>

                    <!-- Bookmark Button -->
                    <button @click="toggleBookmark()"
                        :class="isBookmarked ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-primary-100'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-medium transition-all duration-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                    </button>
                @else
                    <button @click="$store.loginModal = true"
                        class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                        </svg>
                        Login
                    </button>
                @endauth

                <div class="w-px h-8 bg-gray-200"></div>

                <!-- Comment Link -->
                <a href="#comments"
                    class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>{{ $commentsCount }}</span>
                </a>

                <!-- Share Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        class="share-dropdown absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-2">
                        <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . url()->current()) }}"
                            target="_blank" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 text-gray-700">
                            <span class="text-lg">💬</span> WhatsApp
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(url()->current()) }}"
                            target="_blank" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 text-gray-700">
                            <span class="text-lg">🐦</span> Twitter
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                            target="_blank" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 text-gray-700">
                            <span class="text-lg">📘</span> Facebook
                        </a>
                        <button @click="copyLink(); open = false"
                            class="w-full flex items-center gap-3 px-4 py-2 hover:bg-gray-50 text-gray-700">
                            <span class="text-lg">🔗</span> Salin Link
                        </button>
                    </div>
                </div>

                @auth
                    @if(auth()->user()->id === $article->user_id)
                        <a href="{{ route('articles.edit', $article) }}"
                            class="flex items-center gap-2 px-4 py-2.5 bg-primary-500 text-white rounded-xl font-medium hover:bg-primary-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Main Content with Comments -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 mt-16 pt-16 border-t border-gray-100">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Comments Section -->
                <section id="comments" class="flex-1">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-bold font-display text-gray-900">
                            Komentar <span class="text-gray-400">({{ $commentsCount }})</span>
                        </h2>

                        @auth
                            @if(auth()->user()->isAdmin())
                                <!-- Admin: Toggle Comments -->
                                <div
                                    x-data="{ commentsEnabled: {{ $article->comments_enabled ? 'true' : 'false' }}, toggling: false }">
                                    <button @click="toggleArticleComments()" :disabled="toggling"
                                        class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                                        :class="commentsEnabled ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100'">
                                        <svg x-show="!toggling" class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path x-show="commentsEnabled" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            <path x-show="!commentsEnabled" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <svg x-show="toggling" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span x-text="commentsEnabled ? 'Tutup Komentar' : 'Buka Komentar'"></span>
                                    </button>
                                </div>
                            @endif
                        @endauth
                    </div>

                    @if(!$article->comments_enabled)
                        <!-- Comments Closed Notice -->
                        <div class="mb-10 p-6 bg-gray-100 rounded-2xl border border-gray-200 text-center">
                            <div class="flex items-center justify-center gap-3 text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                <p class="font-medium">Komentar untuk artikel ini telah ditutup.</p>
                            </div>
                        </div>
                    @elseif(Auth::check())
                        <!-- Comment Form -->
                        <div class="mb-10 p-6 bg-white rounded-2xl border border-gray-100 shadow-sm">
                            <div class="flex gap-4">
                                <img src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->full_name) . '&background=14b8a6&color=fff' }}"
                                    alt="{{ auth()->user()->full_name }}"
                                    class="w-10 h-10 rounded-xl object-cover flex-shrink-0">
                                <div class="flex-1">
                                    <textarea x-model="commentContent" placeholder="Tulis komentar kamu..."
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 resize-none transition-all"
                                        rows="3"></textarea>
                                    <div class="flex justify-end mt-3">
                                        <button @click="submitComment()"
                                            :disabled="commentLoading || !commentContent.trim() || commentContent.length < 5"
                                            class="px-6 py-2.5 bg-primary-500 text-white rounded-xl font-medium hover:bg-primary-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                            <span x-show="!commentLoading">Kirim Komentar</span>
                                            <span x-show="commentLoading" class="flex items-center gap-2">
                                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                        stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                Mengirim...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mb-10 p-6 bg-primary-50 rounded-2xl border border-primary-100 text-center">
                            <p class="text-primary-700">
                                <a href="#" @click.prevent="$store.loginModal = true"
                                    class="font-semibold hover:underline">Masuk</a> untuk menambahkan komentar.
                            </p>
                        </div>
                    @endif

                    <!-- Comments List -->
                    <div class="space-y-6">
                        @forelse($comments as $comment)
                            @if($comment->deleted_by_admin)
                                <!-- Deleted by Admin Comment -->
                                <div id="comment-{{ $comment->id }}" class="p-6 bg-gray-50 rounded-2xl border border-gray-200">
                                    <div class="flex gap-4">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-gray-200 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                <span class="text-gray-400 italic">Komentar dihapus</span>
                                                <span
                                                    class="text-sm text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-gray-400 italic">Komentar ini telah dihapus oleh Admin.</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div id="comment-{{ $comment->id }}"
                                    x-data="commentItem({{ $comment->id }}, {{ $comment->likes_count }}, {{ $comment->is_liked ? 'true' : 'false' }}, '{{ $comment->user_reaction }}', {{ json_encode($comment->reactions_summary) }}, {{ auth()->check() && auth()->user()->isAdmin() ? 'true' : 'false' }})"
                                    class="group p-6 bg-white rounded-2xl border border-gray-100 hover:border-gray-200 transition-colors">
                                    <div class="flex gap-4">
                                        <img src="{{ $comment->user->avatar ? Storage::url($comment->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->full_name) . '&background=14b8a6&color=fff' }}"
                                            alt="{{ $comment->user->full_name }}"
                                            class="w-10 h-10 rounded-xl object-cover flex-shrink-0">
                                        <div class="flex-1">
                                            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="font-semibold text-gray-900">{{ $comment->user->full_name }}</span>
                                                    @if($comment->user->id === $article->user_id)
                                                        <span
                                                            class="px-2 py-0.5 bg-primary-100 text-primary-700 text-xs font-medium rounded-full">Penulis</span>
                                                    @endif
                                                    <span
                                                        class="text-sm text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                                </div>

                                                @auth
                                                    @if(auth()->user()->isAdmin())
                                                        <!-- Admin Delete Button -->
                                                        <div x-data="{ deleting: false, showConfirm: false }" class="relative">
                                                            <button @click="showConfirm = true"
                                                                class="opacity-0 group-hover:opacity-100 p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>

                                                            <!-- Confirm Dialog -->
                                                            <div x-show="showConfirm" x-transition @click.away="showConfirm = false"
                                                                class="absolute right-0 top-full mt-2 w-64 p-4 bg-white rounded-xl shadow-lg border border-gray-100 z-50">
                                                                <p class="text-sm text-gray-700 mb-3">Hapus komentar ini? Akan diganti
                                                                    dengan keterangan "Dihapus oleh Admin".</p>
                                                                <div class="flex gap-2">
                                                                    <button @click="showConfirm = false"
                                                                        class="flex-1 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                                                        Batal
                                                                    </button>
                                                                    <button @click="adminDeleteComment(); showConfirm = false"
                                                                        :disabled="deleting"
                                                                        class="flex-1 px-3 py-1.5 text-sm bg-red-500 text-white rounded-lg hover:bg-red-600 disabled:opacity-50 transition-colors">
                                                                        <span x-show="!deleting">Hapus</span>
                                                                        <span x-show="deleting">...</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endauth
                                            </div>
                                            <p class="text-gray-700 leading-relaxed mb-3">{{ $comment->content }}</p>

                                            <!-- Reactions Display -->
                                            <div class="flex flex-wrap items-center gap-2 mb-3"
                                                x-show="reactionsSummary.length > 0">
                                                <template x-for="reaction in reactionsSummary" :key="reaction.type">
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-1 bg-gray-50 rounded-full text-sm"
                                                        :class="userReaction === reaction.type ? 'bg-primary-50 ring-1 ring-primary-200' : ''">
                                                        <span x-text="reaction.emoji"></span>
                                                        <span class="text-gray-600" x-text="reaction.count"></span>
                                                    </span>
                                                </template>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="flex items-center gap-4 text-sm">
                                                @auth
                                                    <!-- Like Button -->
                                                    <button @click="toggleLike()"
                                                        class="flex items-center gap-1.5 text-gray-500 hover:text-primary-500 transition-colors"
                                                        :class="isLiked ? 'text-primary-500' : ''" <svg class="w-4 h-4"
                                                        :fill="isLiked ? 'currentColor' : 'none'" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                                        </svg>
                                                        <span x-text="likesCount"></span>
                                                    </button>

                                                    <!-- Reaction Picker -->
                                                    <div class="relative" x-data="{ showPicker: false }">
                                                        <button @click="showPicker = !showPicker" @click.away="showPicker = false"
                                                            class="flex items-center gap-1.5 text-gray-500 hover:text-primary-500 transition-colors">
                                                            <span x-show="!userReaction">😀</span>
                                                            <span x-show="userReaction" x-text="getReactionEmoji(userReaction)"></span>
                                                            <span class="text-xs">Reaksi</span>
                                                        </button>
                                                        <div x-show="showPicker" x-transition
                                                            class="absolute left-0 bottom-full mb-2 bg-white rounded-xl shadow-lg border border-gray-100 p-2 flex gap-1 z-50">
                                                            <button @click="addReaction('like'); showPicker = false"
                                                                class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg transition-colors text-lg"
                                                                :class="userReaction === 'like' ? 'bg-primary-50' : ''">👍</button>
                                                            <button @click="addReaction('love'); showPicker = false"
                                                                class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg transition-colors text-lg"
                                                                :class="userReaction === 'love' ? 'bg-primary-50' : ''">❤️</button>
                                                            <button @click="addReaction('haha'); showPicker = false"
                                                                class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg transition-colors text-lg"
                                                                :class="userReaction === 'haha' ? 'bg-primary-50' : ''">😂</button>
                                                            <button @click="addReaction('wow'); showPicker = false"
                                                                class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg transition-colors text-lg"
                                                                :class="userReaction === 'wow' ? 'bg-primary-50' : ''">😮</button>
                                                            <button @click="addReaction('sad'); showPicker = false"
                                                                class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg transition-colors text-lg"
                                                                :class="userReaction === 'sad' ? 'bg-primary-50' : ''">😢</button>
                                                            <button @click="addReaction('angry'); showPicker = false"
                                                                class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg transition-colors text-lg"
                                                                :class="userReaction === 'angry' ? 'bg-primary-50' : ''">😡</button>
                                                        </div>
                                                    </div>

                                                    @if($article->comments_enabled)
                                                        <!-- Reply Button -->
                                                        <button @click="showReplyForm = !showReplyForm"
                                                            class="flex items-center gap-1.5 text-gray-500 hover:text-primary-500 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                            </svg>
                                                            <span>Balas</span>
                                                        </button>
                                                    @endif
                                                @else
                                                    <a href="#" @click.prevent="$store.loginModal = true"
                                                        class="flex items-center gap-1.5 text-gray-500 hover:text-primary-500 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                                        </svg>
                                                        <span>{{ $comment->likes_count }}</span>
                                                    </a>
                                                @endauth

                                                <!-- Replies Count -->
                                                @if($comment->replies->count() > 0)
                                                    <button
                                                        @click="showReplies = !showReplies; if(showReplies && replies.length === 0) loadReplies()"
                                                        class="flex items-center gap-1.5 text-gray-500 hover:text-primary-500 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                        </svg>
                                                        <span>{{ $comment->replies->count() }} balasan</span>
                                                        <svg class="w-3 h-3 transition-transform"
                                                            :class="showReplies ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>

                                            @auth
                                                @if($article->comments_enabled)
                                                    <!-- Reply Form -->
                                                    <div x-show="showReplyForm" x-transition class="mt-4 pl-4 border-l-2 border-gray-100">
                                                        <div class="flex gap-3">
                                                            <img src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->full_name) . '&background=14b8a6&color=fff' }}"
                                                                alt="{{ auth()->user()->full_name }}"
                                                                class="w-8 h-8 rounded-lg object-cover flex-shrink-0">
                                                            <div class="flex-1">
                                                                <textarea x-model="replyContent" placeholder="Tulis balasan..."
                                                                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 resize-none transition-all"
                                                                    rows="2"></textarea>
                                                                <div class="flex justify-end gap-2 mt-2">
                                                                    <button @click="showReplyForm = false; replyContent = ''"
                                                                        class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800 transition-colors">
                                                                        Batal
                                                                    </button>
                                                                    <button @click="submitReply()"
                                                                        :disabled="replyLoading || !replyContent.trim() || replyContent.length < 5"
                                                                        class="px-4 py-1.5 text-sm bg-primary-500 text-white rounded-lg font-medium hover:bg-primary-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                                                        <span x-show="!replyLoading">Balas</span>
                                                                        <span x-show="replyLoading">...</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endauth

                                            <!-- Nested Replies -->
                                            <div x-show="showReplies" x-transition
                                                class="mt-4 space-y-4 pl-4 border-l-2 border-gray-100">
                                                <!-- Loading State -->
                                                <div x-show="loadingReplies" class="flex items-center gap-2 text-gray-500 text-sm">
                                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                            stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                    Memuat balasan...
                                                </div>

                                                <!-- Replies List -->
                                                <template x-for="reply in replies" :key="reply.id">
                                                    <div class="p-4 bg-gray-50 rounded-xl" :id="'comment-' + reply.id">
                                                        <div class="flex gap-3">
                                                            <img :src="reply.user.avatar" :alt="reply.user.full_name"
                                                                class="w-8 h-8 rounded-lg object-cover flex-shrink-0">
                                                            <div class="flex-1">
                                                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                                                    <span class="font-semibold text-gray-900 text-sm"
                                                                        x-text="reply.user.full_name"></span>
                                                                    <span class="text-xs text-gray-400"
                                                                        x-text="reply.created_at"></span>
                                                                </div>
                                                                <p class="text-gray-700 text-sm leading-relaxed mb-2"
                                                                    x-text="reply.content"></p>

                                                                <!-- Reply Reactions Display -->
                                                                <div class="flex flex-wrap items-center gap-1 mb-2"
                                                                    x-show="reply.reactions_summary && reply.reactions_summary.length > 0">
                                                                    <template x-for="reaction in reply.reactions_summary"
                                                                        :key="reaction.type">
                                                                        <span
                                                                            class="inline-flex items-center gap-0.5 px-1.5 py-0.5 bg-white rounded-full text-xs">
                                                                            <span x-text="reaction.emoji"></span>
                                                                            <span class="text-gray-600"
                                                                                x-text="reaction.count"></span>
                                                                        </span>
                                                                    </template>
                                                                </div>

                                                                @auth
                                                                    <!-- Reply Actions -->
                                                                    <div class="flex items-center gap-3 text-xs">
                                                                        <button @click="toggleReplyLike(reply)"
                                                                            class="flex items-center gap-1 text-gray-500 hover:text-primary-500 transition-colors"
                                                                            :class="reply.is_liked ? 'text-primary-500' : ''">
                                                                            <svg class="w-3.5 h-3.5"
                                                                                :fill="reply.is_liked ? 'currentColor' : 'none'"
                                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                                                            </svg>
                                                                            <span x-text="reply.likes_count"></span>
                                                                        </button>

                                                                        <!-- Nested Reply Reaction Picker -->
                                                                        <div class="relative" x-data="{ showNestedPicker: false }">
                                                                            <button @click="showNestedPicker = !showNestedPicker"
                                                                                @click.away="showNestedPicker = false"
                                                                                class="flex items-center gap-1 text-gray-500 hover:text-primary-500 transition-colors">
                                                                                <span x-show="!reply.user_reaction">😀</span>
                                                                                <span x-show="reply.user_reaction"
                                                                                    x-text="getReactionEmoji(reply.user_reaction)"></span>
                                                                            </button>
                                                                            <div x-show="showNestedPicker" x-transition
                                                                                class="absolute left-0 bottom-full mb-2 bg-white rounded-lg shadow-lg border border-gray-100 p-1.5 flex gap-0.5 z-50">
                                                                                <button
                                                                                    @click="addReplyReaction(reply, 'like'); showNestedPicker = false"
                                                                                    class="w-7 h-7 flex items-center justify-center hover:bg-gray-100 rounded transition-colors">👍</button>
                                                                                <button
                                                                                    @click="addReplyReaction(reply, 'love'); showNestedPicker = false"
                                                                                    class="w-7 h-7 flex items-center justify-center hover:bg-gray-100 rounded transition-colors">❤️</button>
                                                                                <button
                                                                                    @click="addReplyReaction(reply, 'haha'); showNestedPicker = false"
                                                                                    class="w-7 h-7 flex items-center justify-center hover:bg-gray-100 rounded transition-colors">😂</button>
                                                                                <button
                                                                                    @click="addReplyReaction(reply, 'wow'); showNestedPicker = false"
                                                                                    class="w-7 h-7 flex items-center justify-center hover:bg-gray-100 rounded transition-colors">😮</button>
                                                                                <button
                                                                                    @click="addReplyReaction(reply, 'sad'); showNestedPicker = false"
                                                                                    class="w-7 h-7 flex items-center justify-center hover:bg-gray-100 rounded transition-colors">😢</button>
                                                                                <button
                                                                                    @click="addReplyReaction(reply, 'angry'); showNestedPicker = false"
                                                                                    class="w-7 h-7 flex items-center justify-center hover:bg-gray-100 rounded transition-colors">😡</button>
                                                                            </div>
                                                                        </div>

                                                                        @if($article->comments_enabled)
                                                                            <button @click="replyToReply(reply)"
                                                                                class="flex items-center gap-1 text-gray-500 hover:text-primary-500 transition-colors">
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                                        stroke-width="2"
                                                                                        d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                                                </svg>
                                                                                <span>Balas</span>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                @endauth
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>

                                                @auth
                                                    @if($article->comments_enabled)
                                                        <!-- Reply to Reply Form -->
                                                        <div x-show="replyingToReply" x-transition class="p-4 bg-gray-50 rounded-xl">
                                                            <div class="text-xs text-gray-500 mb-2">
                                                                Membalas <span class="font-medium" x-text="replyingToUser"></span>
                                                            </div>
                                                            <div class="flex gap-3">
                                                                <img src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->full_name) . '&background=14b8a6&color=fff' }}"
                                                                    alt="{{ auth()->user()->full_name }}"
                                                                    class="w-8 h-8 rounded-lg object-cover flex-shrink-0">
                                                                <div class="flex-1">
                                                                    <textarea x-model="nestedReplyContent" placeholder="Tulis balasan..."
                                                                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 resize-none transition-all"
                                                                        rows="2"></textarea>
                                                                    <div class="flex justify-end gap-2 mt-2">
                                                                        <button @click="replyingToReply = false; nestedReplyContent = ''"
                                                                            class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800 transition-colors">
                                                                            Batal
                                                                        </button>
                                                                        <button @click="submitNestedReply()"
                                                                            :disabled="nestedReplyLoading || !nestedReplyContent.trim() || nestedReplyContent.length < 5"
                                                                            class="px-4 py-1.5 text-sm bg-primary-500 text-white rounded-lg font-medium hover:bg-primary-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                                                            <span x-show="!nestedReplyLoading">Balas</span>
                                                                            <span x-show="nestedReplyLoading">...</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="py-16 text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Komentar</h3>
                                <p class="text-gray-500">Jadilah yang pertama memberikan komentar!</p>
                            </div>
                        @endforelse

                        <!-- Comment Pagination -->
                        @if($comments->hasPages())
                            <div class="mt-8 pt-6 border-t border-gray-100">
                                <nav class="flex items-center justify-between">
                                    <div class="flex-1 flex justify-between sm:hidden">
                                        @if($comments->onFirstPage())
                                            <span
                                                class="px-4 py-2 text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed">Sebelumnya</span>
                                        @else
                                            <a href="{{ $comments->previousPageUrl() }}#comments"
                                                class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50">Sebelumnya</a>
                                        @endif
                                        @if($comments->hasMorePages())
                                            <a href="{{ $comments->nextPageUrl() }}#comments"
                                                class="ml-3 px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50">Berikutnya</a>
                                        @else
                                            <span
                                                class="ml-3 px-4 py-2 text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed">Berikutnya</span>
                                        @endif
                                    </div>
                                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                        <p class="text-sm text-gray-500">
                                            Menampilkan <span class="font-medium">{{ $comments->firstItem() }}</span> - <span
                                                class="font-medium">{{ $comments->lastItem() }}</span>
                                            dari <span class="font-medium">{{ $comments->total() }}</span> komentar
                                        </p>
                                        <div class="flex gap-1">
                                            @if($comments->onFirstPage())
                                                <span
                                                    class="w-10 h-10 flex items-center justify-center text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 19l-7-7 7-7" />
                                                    </svg>
                                                </span>
                                            @else
                                                <a href="{{ $comments->previousPageUrl() }}#comments"
                                                    class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 19l-7-7 7-7" />
                                                    </svg>
                                                </a>
                                            @endif
                                            @foreach($comments->getUrlRange(max(1, $comments->currentPage() - 2), min($comments->lastPage(), $comments->currentPage() + 2)) as $page => $url)
                                                @if($page == $comments->currentPage())
                                                    <span
                                                        class="w-10 h-10 flex items-center justify-center bg-primary-500 text-white rounded-xl font-medium">{{ $page }}</span>
                                                @else
                                                    <a href="{{ $url }}#comments"
                                                        class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">{{ $page }}</a>
                                                @endif
                                            @endforeach
                                            @if($comments->hasMorePages())
                                                <a href="{{ $comments->nextPageUrl() }}#comments"
                                                    class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </a>
                                            @else
                                                <span
                                                    class="w-10 h-10 flex items-center justify-center text-gray-400 bg-gray-100 rounded-xl cursor-not-allowed">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </nav>
                            </div>
                        @endif
                    </div>
                </section>

                <!-- Sidebar Spacer -->
                <aside class="hidden lg:block lg:w-80 xl:w-96 flex-shrink-0"></aside>
            </div>
        </div>

        <!-- Spacer before footer -->
        <div class="pb-16"></div>
    </article>
@endsection

@section('scripts')
    <script>
        // Reading Progress Bar
        document.addEventListener('scroll', function () {
            const article = document.getElementById('article-content');
            if (!article) return;

            const articleTop = article.offsetTop;
            const articleHeight = article.offsetHeight;
            const windowHeight = window.innerHeight;
            const scrollTop = window.pageYOffset;

            const progress = Math.min(100, Math.max(0,
                ((scrollTop - articleTop + windowHeight) / articleHeight) * 100
            ));

            document.getElementById('reading-progress').style.width = progress + '%';
        });

        // Alpine.js Article Actions
        function articleActions() {
            return {
                isLiked: {{ $isLiked ? 'true' : 'false' }},
                isBookmarked: {{ $isBookmarked ? 'true' : 'false' }},
                likesCount: {{ $article->likedBy()->count() }},
                showFloatingBar: false,
                textSize: localStorage.getItem('articleTextSize') || 'md',
                linkCopied: false,
                commentContent: '',
                commentLoading: false,

                init() {
                    // Show floating bar only when reading article content
                    window.addEventListener('scroll', () => {
                        const article = document.getElementById('article-content');
                        if (article) {
                            const rect = article.getBoundingClientRect();
                            const windowHeight = window.innerHeight;
                            // Show when article content enters the viewport (offset by 200px)
                            // and hide when article content leaves the viewport
                            this.showFloatingBar = rect.top < windowHeight - 200 && rect.bottom > 200;
                        }
                    });

                    // Restore scroll position
                    const savedPosition = sessionStorage.getItem('article_{{ $article->id }}_scroll');
                    if (savedPosition) {
                        setTimeout(() => window.scrollTo(0, parseInt(savedPosition)), 100);
                    }

                    // Save scroll position on unload
                    window.addEventListener('beforeunload', () => {
                        sessionStorage.setItem('article_{{ $article->id }}_scroll', window.scrollY);
                    });
                },

                setTextSize(size) {
                    this.textSize = size;
                    localStorage.setItem('articleTextSize', size);
                },

                async toggleLike() {
                    try {
                        const response = await fetch('{{ route("likes.toggle", $article) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.isLiked = data.liked;
                            this.likesCount = data.likes_count;
                        }
                    } catch (error) {
                        console.error('Error toggling like:', error);
                    }
                },

                async toggleBookmark() {
                    try {
                        const response = await fetch('{{ route("bookmarks.toggle", $article) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.isBookmarked = data.bookmarked;
                        }
                    } catch (error) {
                        console.error('Error toggling bookmark:', error);
                    }
                },

                copyLink() {
                    navigator.clipboard.writeText(window.location.href);
                    this.linkCopied = true;
                    setTimeout(() => this.linkCopied = false, 2000);
                },

                async submitComment() {
                    if (this.commentContent.trim().length < 5) {
                        alert('Komentar minimal 5 karakter');
                        return;
                    }

                    this.commentLoading = true;
                    try {
                        const response = await fetch('{{ route("comments.store", $article) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ content: this.commentContent })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.commentContent = '';
                            location.reload();
                        }
                    } catch (error) {
                        console.error('Error submitting comment:', error);
                    } finally {
                        this.commentLoading = false;
                    }
                }
            }
        }

        // Toggle Article Comments (Admin only)
        async function toggleArticleComments() {
            const button = event.target.closest('div[x-data]');
            if (!button) return;

            try {
                const response = await fetch('{{ route("articles.toggle-comments", $article->slug) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
                });
                const data = await response.json();
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Gagal mengubah status komentar');
                }
            } catch (error) {
                console.error('Error toggling comments:', error);
                alert('Terjadi kesalahan');
            }
        }

        // Comment Item Alpine Component
        function commentItem(commentId, initialLikesCount, initialIsLiked, initialUserReaction, initialReactionsSummary, isAdmin = false) {
            return {
                commentId: commentId,
                likesCount: initialLikesCount,
                isLiked: initialIsLiked,
                userReaction: initialUserReaction || null,
                reactionsSummary: initialReactionsSummary || [],
                isAdmin: isAdmin,
                showReplyForm: false,
                showReplies: false,
                replyContent: '',
                replyLoading: false,
                replies: [],
                loadingReplies: false,
                replyingToReply: false,
                replyingToUser: '',
                replyingToId: null,
                nestedReplyContent: '',
                nestedReplyLoading: false,

                async adminDeleteComment() {
                    try {
                        const response = await fetch(`/komentar/${this.commentId}/hapus-admin`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Gagal menghapus komentar');
                        }
                    } catch (error) {
                        console.error('Error deleting comment:', error);
                        alert('Terjadi kesalahan');
                    }
                },

                getReactionEmoji(type) {
                    const emojis = {
                        'like': '👍',
                        'love': '❤️',
                        'haha': '😂',
                        'wow': '😮',
                        'sad': '😢',
                        'angry': '😡'
                    };
                    return emojis[type] || '👍';
                },

                async toggleLike() {
                    try {
                        const response = await fetch(`/komentar/${this.commentId}/like`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.isLiked = data.is_liked;
                            this.likesCount = data.likes_count;
                        }
                    } catch (error) {
                        console.error('Error toggling like:', error);
                    }
                },

                async addReaction(reactionType) {
                    try {
                        const response = await fetch(`/komentar/${this.commentId}/reaksi`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ reaction: reactionType })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.userReaction = data.user_reaction;
                            this.reactionsSummary = data.reactions_summary;
                        }
                    } catch (error) {
                        console.error('Error adding reaction:', error);
                    }
                },

                async loadReplies() {
                    this.loadingReplies = true;
                    try {
                        const response = await fetch(`/komentar/${this.commentId}/balasan`, {
                            headers: {
                                'Accept': 'application/json',
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.replies = data.replies;
                        }
                    } catch (error) {
                        console.error('Error loading replies:', error);
                    } finally {
                        this.loadingReplies = false;
                    }
                },

                async submitReply() {
                    if (this.replyContent.trim().length < 5) {
                        alert('Balasan minimal 5 karakter');
                        return;
                    }

                    this.replyLoading = true;
                    try {
                        const response = await fetch('{{ route("comments.store", $article) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                content: this.replyContent,
                                parent_id: this.commentId
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.replyContent = '';
                            this.showReplyForm = false;
                            // Reload replies
                            this.showReplies = true;
                            await this.loadReplies();
                        }
                    } catch (error) {
                        console.error('Error submitting reply:', error);
                    } finally {
                        this.replyLoading = false;
                    }
                },

                async toggleReplyLike(reply) {
                    try {
                        const response = await fetch(`/komentar/${reply.id}/like`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            reply.is_liked = data.is_liked;
                            reply.likes_count = data.likes_count;
                        }
                    } catch (error) {
                        console.error('Error toggling reply like:', error);
                    }
                },

                async addReplyReaction(reply, reactionType) {
                    try {
                        const response = await fetch(`/komentar/${reply.id}/reaksi`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ reaction: reactionType })
                        });
                        const data = await response.json();
                        if (data.success) {
                            reply.user_reaction = data.user_reaction;
                            reply.reactions_summary = data.reactions_summary;
                        }
                    } catch (error) {
                        console.error('Error adding reply reaction:', error);
                    }
                },

                replyToReply(reply) {
                    this.replyingToReply = true;
                    this.replyingToUser = reply.user.full_name;
                    this.replyingToId = reply.id;
                    this.nestedReplyContent = `@${reply.user.full_name} `;
                },

                async submitNestedReply() {
                    if (this.nestedReplyContent.trim().length < 5) {
                        alert('Balasan minimal 5 karakter');
                        return;
                    }

                    this.nestedReplyLoading = true;
                    try {
                        const response = await fetch('{{ route("comments.store", $article) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                content: this.nestedReplyContent,
                                parent_id: this.commentId // Still reply to parent comment
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.nestedReplyContent = '';
                            this.replyingToReply = false;
                            // Reload replies
                            await this.loadReplies();
                        }
                    } catch (error) {
                        console.error('Error submitting nested reply:', error);
                    } finally {
                        this.nestedReplyLoading = false;
                    }
                }
            }
        }
    </script>
@endsection