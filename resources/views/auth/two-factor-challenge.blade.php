@extends('layouts.guest')

@section('title', 'Verifikasi Two-Factor - IDN Menulis')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-primary-50/30 to-accent-50/20 px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-8 py-10">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 font-display">Verifikasi Two-Factor</h1>
                    <p class="mt-2 text-gray-600">Masukkan kode dari aplikasi authenticator Anda</p>
                </div>

                <form action="{{ route('two-factor.verify') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode Authenticator</label>
                        <input type="text" name="code" maxlength="8" required autofocus
                               class="w-full px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent text-center text-2xl tracking-widest font-mono @error('code') border-red-300 @enderror"
                               placeholder="000000"
                               autocomplete="one-time-code">
                        @error('code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500 text-center">Atau gunakan salah satu recovery code Anda</p>
                    </div>

                    <button type="submit" class="w-full bg-primary-500 hover:bg-primary-600 text-white font-semibold py-4 px-6 rounded-xl transition-colors">
                        Verifikasi
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700 text-sm">
                            Login dengan akun lain
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <p class="text-center text-sm text-gray-500 mt-4">
            Tidak bisa mengakses authenticator?
            <a href="#" class="text-primary-600 hover:underline" onclick="document.getElementById('recovery-help').classList.toggle('hidden')">
                Gunakan Recovery Code
            </a>
        </p>

        <div id="recovery-help" class="hidden mt-4 bg-white rounded-xl p-4 shadow-lg text-sm text-gray-600">
            <p class="mb-2">Jika Anda kehilangan akses ke aplikasi authenticator:</p>
            <ol class="list-decimal list-inside space-y-1">
                <li>Masukkan salah satu recovery code yang Anda simpan</li>
                <li>Recovery code hanya bisa digunakan sekali</li>
                <li>Setelah login, segera setup ulang 2FA Anda</li>
            </ol>
        </div>
    </div>
</div>
@endsection
