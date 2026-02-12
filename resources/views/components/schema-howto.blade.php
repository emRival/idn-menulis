@props([
    'name',
    'description' => '',
    'image' => null,
    'totalTime' => null,
    'steps' => []
])

@if(count($steps) > 0)
{{-- Visible HowTo Section --}}
<div class="howto-section" itemscope itemtype="https://schema.org/HowTo">
    <h2 itemprop="name" class="text-2xl font-bold text-gray-900 mb-2">{{ $name }}</h2>

    @if($description)
    <p itemprop="description" class="text-gray-600 mb-6">{{ $description }}</p>
    @endif

    @if($totalTime)
    <div class="flex items-center text-sm text-gray-500 mb-4">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <meta itemprop="totalTime" content="{{ $totalTime }}">
        Waktu yang dibutuhkan: <span class="font-medium ml-1">{{ $totalTime }}</span>
    </div>
    @endif

    @if($image)
    <img itemprop="image" src="{{ $image }}" alt="{{ $name }}" class="w-full h-auto rounded-lg mb-6" loading="lazy">
    @endif

    <ol class="space-y-6">
        @foreach($steps as $index => $step)
        <li class="howto-step" itemprop="step" itemscope itemtype="https://schema.org/HowToStep">
            <meta itemprop="position" content="{{ $index + 1 }}">
            <div class="flex items-start">
                <span class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold mr-4">
                    {{ $index + 1 }}
                </span>
                <div class="flex-1">
                    <h3 itemprop="name" class="font-semibold text-gray-900 mb-2">{{ $step['name'] }}</h3>
                    <div itemprop="text" class="text-gray-600">{{ $step['text'] }}</div>
                    @if(!empty($step['image']))
                    <img itemprop="image" src="{{ $step['image'] }}" alt="{{ $step['name'] }}" class="mt-4 rounded-lg max-w-md" loading="lazy">
                    @endif
                </div>
            </div>
        </li>
        @endforeach
    </ol>
</div>

{{-- JSON-LD Schema --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "HowTo",
    "name": "{{ e($name) }}",
    "description": "{{ e($description) }}",
    @if($image)
    "image": "{{ $image }}",
    @endif
    @if($totalTime)
    "totalTime": "{{ $totalTime }}",
    @endif
    "step": [
        @foreach($steps as $index => $step)
        {
            "@type": "HowToStep",
            "position": {{ $index + 1 }},
            "name": "{{ e($step['name']) }}",
            "text": "{{ e($step['text']) }}"
            @if(!empty($step['image']))
            ,"image": "{{ $step['image'] }}"
            @endif
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endif
