<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach($articles as $article)
    <url>
        <loc>{{ $article['loc'] }}</loc>
        <lastmod>{{ $article['lastmod'] }}</lastmod>
        <changefreq>{{ $article['changefreq'] }}</changefreq>
        <priority>{{ $article['priority'] }}</priority>
        @if(!empty($article['image']))
        <image:image>
            <image:loc>{{ str_starts_with($article['image'], 'http') ? $article['image'] : url($article['image']) }}</image:loc>
            <image:title>{{ e($article['title']) }}</image:title>
        </image:image>
        @endif
    </url>
    @endforeach
</urlset>
