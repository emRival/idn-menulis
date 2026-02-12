@props([
    'reading_time' => 0,
    'word_count' => 0,
    'views' => 0
])

<div class="flex flex-wrap items-center text-sm text-gray-500 gap-4">
    @if($reading_time > 0)
    <div class="flex items-center" title="Waktu baca">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>{{ $reading_time }} menit baca</span>
    </div>
    @endif

    @if($word_count > 0)
    <div class="flex items-center" title="Jumlah kata">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <span>{{ number_format($word_count) }} kata</span>
    </div>
    @endif

    @if($views > 0)
    <div class="flex items-center" title="Dilihat">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
        </svg>
        <span>{{ number_format($views) }}x dilihat</span>
    </div>
    @endif
</div>
