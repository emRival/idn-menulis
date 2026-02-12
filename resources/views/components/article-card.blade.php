@props([
    'article',
    'showImage' => true,
    'showMeta' => true,
    'showExcerpt' => true,
    'imageSize' => 'medium' // small, medium, large
])

@php
    use Illuminate\Support\Str;

    $imageClasses = [
        'small' => 'w-full h-32 object-cover',
        'medium' => 'w-full h-48 object-cover',
        'large' => 'w-full h-64 object-cover',
    ];

    $excerpt = $article->excerpt ?? Str::limit(strip_tags($article->content), 150);
    $url = route('articles.show', $article->slug);
    $categoryUrl = $article->category ? route('categories.show', $article->category->slug) : '#';
    $readingTime = max(1, ceil(str_word_count(strip_tags($article->content ?? '')) / 200));
@endphp

<article class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow" itemscope itemtype="https://schema.org/Article">
    @if($showImage && $article->featured_image)
    <a href="{{ $url }}" class="block">
        <img
            src="{{ str_starts_with($article->featured_image, 'http') ? $article->featured_image : asset($article->featured_image) }}"
            alt="{{ $article->title }}"
            class="{{ $imageClasses[$imageSize] ?? $imageClasses['medium'] }}"
            loading="lazy"
            decoding="async"
            itemprop="image"
        >
    </a>
    @endif

    <div class="p-4">
        @if($showMeta && $article->category)
        <div class="flex items-center text-sm text-gray-500 mb-2">
            <a href="{{ $categoryUrl }}" class="text-blue-600 hover:underline" itemprop="articleSection">
                {{ $article->category->name }}
            </a>
            <span class="mx-2">•</span>
            <time datetime="{{ $article->created_at->toIso8601String() }}" itemprop="datePublished">
                {{ $article->created_at->format('d M Y') }}
            </time>
            <span class="mx-2">•</span>
            <span>{{ $readingTime }} menit baca</span>
        </div>
        @endif

        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
            <a href="{{ $url }}" class="hover:text-blue-600" itemprop="headline">
                {{ $article->title }}
            </a>
        </h3>

        @if($showExcerpt)
        <p class="text-gray-600 text-sm line-clamp-3" itemprop="description">
            {{ $excerpt }}
        </p>
        @endif

        @if($article->author)
        <div class="mt-4 flex items-center" itemprop="author" itemscope itemtype="https://schema.org/Person">
            @if($article->author->avatar)
            <img
                src="{{ asset($article->author->avatar) }}"
                alt="{{ $article->author->name }}"
                class="w-8 h-8 rounded-full mr-2"
                loading="lazy"
            >
            @else
            <div class="w-8 h-8 rounded-full bg-gray-300 mr-2 flex items-center justify-center">
                <span class="text-sm text-gray-600">{{ substr($article->author->name, 0, 1) }}</span>
            </div>
            @endif
            <span class="text-sm text-gray-700" itemprop="name">{{ $article->author->name }}</span>
        </div>
        @endif
    </div>

    {{-- Hidden structured data --}}
    <meta itemprop="url" content="{{ $url }}">
    @if($article->updated_at)
    <meta itemprop="dateModified" content="{{ $article->updated_at->toIso8601String() }}">
    @endif
</article>
