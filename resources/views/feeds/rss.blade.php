<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:media="http://search.yahoo.com/mrss/">
    <channel>
        <title>{{ $siteName }}</title>
        <description>{{ $siteDescription }}</description>
        <link>{{ $siteUrl }}</link>
        <atom:link href="{{ route('feed.rss') }}" rel="self" type="application/rss+xml"/>
        <language>id</language>
        <copyright>Copyright {{ date('Y') }} {{ $siteName }}</copyright>
        <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
        <generator>{{ $siteName }}</generator>
        <ttl>60</ttl>
        <image>
            <url>{{ url(config('seo.logo')) }}</url>
            <title>{{ $siteName }}</title>
            <link>{{ $siteUrl }}</link>
        </image>

        @foreach($articles as $article)
        <item>
            <title><![CDATA[{{ $article->title }}]]></title>
            <link>{{ route('articles.show', $article->slug) }}</link>
            <guid isPermaLink="true">{{ route('articles.show', $article->slug) }}</guid>
            <description><![CDATA[{{ $article->excerpt ?? Str::limit(strip_tags($article->content), 300) }}]]></description>
            <content:encoded><![CDATA[{!! $article->content !!}]]></content:encoded>
            <pubDate>{{ $article->created_at->toRfc2822String() }}</pubDate>
            @if($article->author)
            <dc:creator><![CDATA[{{ $article->author->name }}]]></dc:creator>
            @endif
            @if($article->category)
            <category><![CDATA[{{ $article->category->name }}]]></category>
            @endif
            @if($article->featured_image)
            <media:content
                url="{{ str_starts_with($article->featured_image, 'http') ? $article->featured_image : url($article->featured_image) }}"
                medium="image"/>
            <enclosure
                url="{{ str_starts_with($article->featured_image, 'http') ? $article->featured_image : url($article->featured_image) }}"
                type="image/jpeg"/>
            @endif
        </item>
        @endforeach
    </channel>
</rss>
