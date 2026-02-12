@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan')
@section('code', '404')
@section('message', 'Halaman Tidak Ditemukan')
@section('description', 'Maaf, halaman yang Anda cari tidak dapat ditemukan. Mungkin halaman tersebut telah dipindahkan atau dihapus.')

@section('image')
    <svg class="error-illustration text-primary-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"
        stroke-width="0.5">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-10">
        <span class="text-[12rem] font-bold text-gray-900">404</span>
    </div>
@endsection