@props([
    'items' => []
])

@if(count($items) > 0)
<nav aria-label="Breadcrumb" class="breadcrumb-nav">
    <ol class="flex flex-wrap items-center space-x-2 text-sm text-gray-600" itemscope itemtype="https://schema.org/BreadcrumbList">
        @foreach($items as $index => $item)
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center">
                @if(!$loop->first)
                    <svg class="w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                @endif

                @if($loop->last || empty($item['url']))
                    <span itemprop="name" class="font-medium text-gray-900" aria-current="page">
                        {{ $item['name'] }}
                    </span>
                @else
                    <a href="{{ $item['url'] }}" itemprop="item" class="hover:text-blue-600 transition-colors">
                        <span itemprop="name">{{ $item['name'] }}</span>
                    </a>
                @endif
                <meta itemprop="position" content="{{ $index + 1 }}">
            </li>
        @endforeach
    </ol>
</nav>

{{-- JSON-LD Schema for Breadcrumbs --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        @foreach($items as $index => $item)
        {
            "@type": "ListItem",
            "position": {{ $index + 1 }},
            "name": "{{ $item['name'] }}"
            @if(!empty($item['url']))
            ,"item": "{{ $item['url'] }}"
            @endif
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endif
