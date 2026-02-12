@props([
    'src',
    'alt' => '',
    'width' => null,
    'height' => null,
    'class' => '',
    'sizes' => '100vw',
    'srcset' => null,
    'loading' => 'lazy',
    'decoding' => 'async'
])

@php
    $isExternal = str_starts_with($src, 'http');
    $imageUrl = $isExternal ? $src : asset($src);

    // Generate WebP URL
    $webpUrl = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $imageUrl);
    $hasWebP = !$isExternal && preg_match('/\.(jpg|jpeg|png|gif)$/i', $src);
@endphp

@if($hasWebP)
<picture>
    <source srcset="{{ $webpUrl }}" type="image/webp">
    <img
        src="{{ $imageUrl }}"
        alt="{{ $alt }}"
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        class="{{ $class }}"
        loading="{{ $loading }}"
        decoding="{{ $decoding }}"
        @if($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
    >
</picture>
@else
<img
    src="{{ $imageUrl }}"
    alt="{{ $alt }}"
    @if($width) width="{{ $width }}" @endif
    @if($height) height="{{ $height }}" @endif
    class="{{ $class }}"
    loading="{{ $loading }}"
    decoding="{{ $decoding }}"
    @if($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
>
@endif
