@extends('layouts.app')

@section('title', 'SEO Analyzer Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">SEO Analyzer Dashboard</h1>
        <p class="text-gray-600 mt-2">Analisis dan optimasi SEO untuk semua artikel</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total_articles'] }}</div>
            <div class="text-sm text-gray-600">Total Artikel</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="text-2xl font-bold text-green-600">{{ $stats['good_seo'] }}</div>
            <div class="text-sm text-gray-600">SEO Baik (80%+)</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['needs_improvement'] }}</div>
            <div class="text-sm text-gray-600">Perlu Perbaikan</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="text-2xl font-bold text-red-600">{{ $stats['poor_seo'] }}</div>
            <div class="text-sm text-gray-600">SEO Buruk (<50%)</div>
        </div>
    </div>

    {{-- Articles Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Analisis Artikel</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Artikel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Skor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kata</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Masalah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($articles as $item)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ Str::limit($item['article']->title, 50) }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $item['article']->created_at->format('d M Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="h-2 rounded-full @if($item['analysis']['overall_score'] >= 80) bg-green-500 @elseif($item['analysis']['overall_score'] >= 50) bg-yellow-500 @else bg-red-500 @endif"
                                         style="width: {{ $item['analysis']['overall_score'] }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ $item['analysis']['overall_score'] }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($item['analysis']['grade'] === 'A') bg-green-100 text-green-800
                                @elseif($item['analysis']['grade'] === 'B') bg-yellow-100 text-yellow-800
                                @elseif($item['analysis']['grade'] === 'C') bg-orange-100 text-orange-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $item['analysis']['grade'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $item['analysis']['word_count'] }}
                        </td>
                        <td class="px-6 py-4">
                            @if(count($item['analysis']['issues']) > 0)
                            <ul class="text-xs text-red-600 space-y-1">
                                @foreach(array_slice($item['analysis']['issues'], 0, 2) as $issue)
                                <li>• {{ $issue }}</li>
                                @endforeach
                                @if(count($item['analysis']['issues']) > 2)
                                <li class="text-gray-500">+{{ count($item['analysis']['issues']) - 2 }} lainnya</li>
                                @endif
                            </ul>
                            @else
                            <span class="text-xs text-green-600">✓ Tidak ada masalah</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('articles.edit', $item['article']->id) }}"
                               class="text-blue-600 hover:text-blue-800 mr-3">Edit</a>
                            <a href="{{ route('articles.show', $item['article']->slug) }}"
                               target="_blank"
                               class="text-gray-600 hover:text-gray-800">Lihat</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- SEO Checklist --}}
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">SEO Checklist Global</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-medium text-gray-800 mb-3">Technical SEO</h3>
                <ul class="space-y-2">
                    <li class="flex items-center text-sm">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Sitemap XML tersedia
                    </li>
                    <li class="flex items-center text-sm">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        robots.txt dikonfigurasi
                    </li>
                    <li class="flex items-center text-sm">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        RSS Feed tersedia
                    </li>
                    <li class="flex items-center text-sm">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Schema.org structured data
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="font-medium text-gray-800 mb-3">Social & Performance</h3>
                <ul class="space-y-2">
                    <li class="flex items-center text-sm">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Open Graph meta tags
                    </li>
                    <li class="flex items-center text-sm">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Twitter Cards
                    </li>
                    <li class="flex items-center text-sm">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Lazy loading gambar
                    </li>
                    <li class="flex items-center text-sm">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        WebP image optimization
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
