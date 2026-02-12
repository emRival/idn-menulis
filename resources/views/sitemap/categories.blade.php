<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($categories as $category)
    <url>
        <loc>{{ $category['loc'] }}</loc>
        <lastmod>{{ $category['lastmod'] }}</lastmod>
        <changefreq>{{ $category['changefreq'] }}</changefreq>
        <priority>{{ $category['priority'] }}</priority>
    </url>
    @endforeach
</urlset>
