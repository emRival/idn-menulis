@extends('layouts.app')

@section('title', 'Kategori Artikel - IDN Menulis')
@section('meta_title', 'Kategori Artikel - IDN Menulis')
@section('meta_description', 'Jelajahi berbagai kategori artikel menarik di IDN Menulis.')

@section('content')
    <!-- Hero Header -->
    <section class="bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl lg:text-5xl font-bold font-display text-white mb-4">Kategori Artikel</h1>
            <p class="text-primary-100 text-lg max-w-2xl mx-auto">Temukan artikel menarik berdasarkan topik yang Anda sukai
            </p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($categories as $category)
                <a href="{{ route('categories.show', $category->slug) }}"
                    class="group bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 card-hover border border-gray-100 block h-full">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            {{ $category->icon ?? '📁' }}
                        </div>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                            {{ $category->articles_count }} Artikel
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 group-hover:text-primary-600 transition-colors mb-2">
                        {{ $category->name }}
                    </h3>

                    @if($category->description)
                        <p class="text-gray-500 text-sm line-clamp-3">
                            {{ $category->description }}
                        </p>
                    @else
                        <p class="text-gray-400 text-sm italic">
                            Tidak ada deskripsi
                        </p>
                    @endif

                    <div
                        class="mt-4 pt-4 border-t border-gray-100 flex items-center text-sm text-primary-600 font-medium group-hover:translate-x-1 transition-transform">
                        Lihat Artikel
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-16 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="text-4xl">📁</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Kategori</h3>
                    <p class="text-gray-500">Belum ada kategori yang dibuat saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection