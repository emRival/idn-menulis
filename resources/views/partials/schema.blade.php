<!-- Website Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "IDN Menulis",
    "url": "{{ config('app.url') }}",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ config('app.url') }}/search?q={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>

@if(isset($article))
<!-- Article Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "{{ $article->title }}",
    "description": "{{ Str::limit(strip_tags($article->content), 160) }}",
    "image": "{{ $article->featured_image }}",
    "author": {
        "@type": "Person",
        "name": "{{ $article->author->name }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "IDN Menulis",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('images/logo.png') }}"
        }
    },
    "datePublished": "{{ $article->created_at->toIso8601String() }}",
    "dateModified": "{{ $article->updated_at->toIso8601String() }}",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ url()->current() }}"
    }
}
</script>
@endif

<!-- Organization Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "IDN Menulis",
    "url": "{{ config('app.url') }}",
    "logo": "{{ asset('images/logo.png') }}",
    "sameAs": [
        "https://facebook.com/idnmenulis",
        "https://twitter.com/idnmenulis",
        "https://instagram.com/idnmenulis"
    ]
}
</script>
