@extends('errors.layout')

@section('title', 'Kesalahan Server')
@section('code', '500')
@section('message', 'Terjadi Kesalahan Server')
@section('description', 'Maaf, terjadi kesalahan pada server kami. Kami sedang bekerja untuk memperbaikinya. Silakan coba lagi nanti.')

@section('image')
    <div class="relative w-48 h-48 mx-auto mb-6 flex items-center justify-center">
        <div class="absolute inset-0 bg-amber-100 rounded-full animate-pulse"></div>
        <svg class="w-24 h-24 text-amber-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
    </div>
@endsection