@props([
    'article',
    'author' => null,
    'category' => null,
    'tags' => []
])

@php
    $articleUrl = route('articles.show', $article->slug);
    $imageUrl = $article->featured_image
        ? (str_starts_with($article->featured_image, 'http') ? $article->featured_image : url($article->featured_image))
        : url(config('seo.default_image'));
    $authorName = $author?->name ?? $article->author?->name ?? config('seo.site_author');
    $authorUrl = $author ? route('profile.show', $author->id) :
        ($article->author ? route('profile.show', $article->author->id) : config('seo.site_url'));
@endphp

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "{{ e($article->title) }}",
    "description": "{{ e($article->excerpt ?? Str::limit(strip_tags($article->content), 160)) }}",
    "url": "{{ $articleUrl }}",
    "datePublished": "{{ $article->created_at->toIso8601String() }}",
    "dateModified": "{{ $article->updated_at->toIso8601String() }}",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ $articleUrl }}"
    },
    "author": {
        "@type": "Person",
        "name": "{{ e($authorName) }}",
        "url": "{{ $authorUrl }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "{{ config('seo.site_name') }}",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ url(config('seo.logo')) }}"
        }
    },
    "image": {
        "@type": "ImageObject",
        "url": "{{ $imageUrl }}",
        "width": 1200,
        "height": 630
    }
    @if($category)
    ,"articleSection": "{{ e($category->name ?? $category) }}"
    @endif
    @if(!empty($tags))
    ,"keywords": "{{ is_array($tags) ? implode(', ', collect($tags)->pluck('name')->toArray()) : $tags }}"
    @endif
    ,"wordCount": {{ str_word_count(strip_tags($article->content)) }}
    ,"inLanguage": "id-ID"
}
</script>
