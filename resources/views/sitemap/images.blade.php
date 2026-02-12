<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach($articles as $article)
    <url>
        <loc>{{ $article['loc'] }}</loc>
        <image:image>
            <image:loc>{{ $article['image_loc'] }}</image:loc>
            <image:title>{{ e($article['image_title']) }}</image:title>
            <image:caption>{{ e($article['image_caption']) }}</image:caption>
        </image:image>
    </url>
    @endforeach
</urlset>
