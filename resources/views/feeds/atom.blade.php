<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<feed xmlns="http://www.w3.org/2005/Atom">
    <id>{{ $siteUrl }}/</id>
    <title>{{ $siteName }}</title>
    <subtitle>{{ $siteDescription }}</subtitle>
    <link href="{{ route('feed.atom') }}" rel="self" type="application/atom+xml"/>
    <link href="{{ $siteUrl }}" rel="alternate" type="text/html"/>
    <updated>{{ now()->toIso8601String() }}</updated>
    <icon>{{ url(config('seo.favicon')) }}</icon>
    <logo>{{ url(config('seo.logo')) }}</logo>
    <rights>Copyright {{ date('Y') }} {{ $siteName }}</rights>
    <generator>{{ $siteName }}</generator>

    @foreach($articles as $article)
    <entry>
        <id>{{ route('articles.show', $article->slug) }}</id>
        <title><![CDATA[{{ $article->title }}]]></title>
        <link href="{{ route('articles.show', $article->slug) }}" rel="alternate" type="text/html"/>
        <published>{{ $article->created_at->toIso8601String() }}</published>
        <updated>{{ $article->updated_at->toIso8601String() }}</updated>
        <summary type="html"><![CDATA[{{ $article->excerpt ?? Str::limit(strip_tags($article->content), 300) }}]]></summary>
        <content type="html"><![CDATA[{!! $article->content !!}]]></content>
        @if($article->author)
        <author>
            <name>{{ $article->author->name }}</name>
            <uri>{{ route('profile.show', $article->author->id) }}</uri>
        </author>
        @endif
        @if($article->category)
        <category term="{{ $article->category->name }}" label="{{ $article->category->name }}"/>
        @endif
        @if($article->featured_image)
        <link rel="enclosure"
              type="image/jpeg"
              href="{{ str_starts_with($article->featured_image, 'http') ? $article->featured_image : url($article->featured_image) }}"/>
        @endif
    </entry>
    @endforeach
</feed>
