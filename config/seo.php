<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SEO Configuration
    |--------------------------------------------------------------------------
    */

    // Site Information
    'site_name' => env('APP_NAME', 'IDN Menulis'),
    'site_description' => env('SEO_DESCRIPTION', 'Platform menulis dan berbagi cerita terbaik di Indonesia. Tulis, baca, dan bagikan karya tulismu.'),
    'site_keywords' => env('SEO_KEYWORDS', 'menulis, blog, artikel, cerita, indonesia, penulis, literasi'),
    'site_author' => env('SEO_AUTHOR', 'IDN Menulis'),
    'site_url' => env('APP_URL', 'https://idnmenulis.com'),

    // Social Media
    'social' => [
        'facebook' => env('SOCIAL_FACEBOOK', 'https://facebook.com/idnmenulis'),
        'twitter' => env('SOCIAL_TWITTER', '@idnmenulis'),
        'twitter_site' => env('SOCIAL_TWITTER_SITE', '@idnmenulis'),
        'instagram' => env('SOCIAL_INSTAGRAM', 'https://instagram.com/idnmenulis'),
        'youtube' => env('SOCIAL_YOUTUBE', ''),
        'linkedin' => env('SOCIAL_LINKEDIN', ''),
    ],

    // Default Images
    'default_image' => '/images/og-default.jpg',
    'logo' => '/images/logo.png',
    'favicon' => '/favicon.ico',

    // Content Settings
    'content' => [
        'min_word_count' => 300,
        'optimal_word_count' => 800,
        'max_word_count' => 2500,
        'keyword_density_min' => 0.5,
        'keyword_density_max' => 2.5,
        'meta_title_max' => 60,
        'meta_description_max' => 160,
        'meta_description_min' => 120,
    ],

    // Sitemap Settings
    'sitemap' => [
        'cache_duration' => 60, // minutes
        'max_urls' => 50000,
        'default_priority' => 0.5,
        'default_changefreq' => 'weekly',
        'priorities' => [
            'home' => 1.0,
            'category' => 0.8,
            'article' => 0.7,
            'tag' => 0.6,
            'page' => 0.5,
            'author' => 0.4,
        ],
        'changefreq' => [
            'home' => 'daily',
            'category' => 'weekly',
            'article' => 'monthly',
            'tag' => 'weekly',
        ],
    ],

    // Robots Settings
    'robots' => [
        'noindex_paths' => [
            '/admin/*',
            '/login',
            '/register',
            '/password/*',
            '/api/*',
            '/sanctum/*',
            '/storage/*',
        ],
        'crawl_delay' => 1,
    ],

    // Structured Data
    'organization' => [
        'name' => env('APP_NAME', 'IDN Menulis'),
        'legal_name' => env('ORG_LEGAL_NAME', 'IDN Menulis'),
        'founding_date' => env('ORG_FOUNDING_DATE', '2024'),
        'founders' => [],
        'address' => [
            'street' => env('ORG_STREET', ''),
            'city' => env('ORG_CITY', 'Jakarta'),
            'region' => env('ORG_REGION', 'DKI Jakarta'),
            'postal_code' => env('ORG_POSTAL', ''),
            'country' => 'ID',
        ],
        'contact' => [
            'email' => env('ORG_EMAIL', 'info@idnmenulis.com'),
            'phone' => env('ORG_PHONE', ''),
        ],
    ],

    // Performance
    'performance' => [
        'lazy_load_images' => true,
        'defer_js' => true,
        'preload_fonts' => true,
        'minify_html' => env('SEO_MINIFY_HTML', false),
        'image_quality' => 85,
        'webp_enabled' => true,
    ],

    // Redirects & Canonicals
    'canonical' => [
        'enforce' => true,
        'trailing_slash' => false,
        'lowercase' => true,
        'remove_index' => true,
    ],

    // AI Search Optimization
    'ai_search' => [
        'summary_length' => 150, // characters for AI summary
        'answer_length' => [40, 60], // words for featured snippet
        'enable_faq_schema' => true,
        'enable_howto_schema' => true,
    ],

    // Analytics & Tracking
    'analytics' => [
        'google_analytics_id' => env('GOOGLE_ANALYTICS_ID', ''),
        'google_tag_manager_id' => env('GOOGLE_TAG_MANAGER_ID', ''),
        'facebook_pixel_id' => env('FACEBOOK_PIXEL_ID', ''),
    ],

    // Search Console
    'verification' => [
        'google' => env('GOOGLE_SITE_VERIFICATION', ''),
        'bing' => env('BING_SITE_VERIFICATION', ''),
        'yandex' => env('YANDEX_VERIFICATION', ''),
    ],
];
