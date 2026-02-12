<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($authors as $author)
    <url>
        <loc>{{ $author['loc'] }}</loc>
        <lastmod>{{ $author['lastmod'] }}</lastmod>
        <changefreq>{{ $author['changefreq'] }}</changefreq>
        <priority>{{ $author['priority'] }}</priority>
    </url>
    @endforeach
</urlset>
