@extends('layouts.app')

@section('title', 'Two-Factor Authentication - IDN Menulis')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-primary-50/30 to-accent-50/20 py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-8 border-b border-gray-100">
                <h1 class="text-2xl font-bold text-gray-900 font-display">Two-Factor Authentication</h1>
                <p class="mt-2 text-gray-600">Tambahkan lapisan keamanan ekstra ke akun Anda</p>
            </div>

            <div class="p-6 space-y-6">
                @if($enabled)
                    <!-- 2FA Enabled State -->
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-green-800">Two-Factor Authentication Aktif</h3>
                            <p class="text-sm text-green-700 mt-1">Akun Anda dilindungi dengan verifikasi dua langkah.</p>
                        </div>
                    </div>

                    <!-- Recovery Codes -->
                    @if(count($recoveryCodes) > 0)
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <h3 class="font-semibold text-amber-800 mb-2">Recovery Codes</h3>
                        <p class="text-sm text-amber-700 mb-4">Simpan kode-kode ini di tempat yang aman. Anda dapat menggunakan salah satu kode ini jika kehilangan akses ke aplikasi authenticator.</p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($recoveryCodes as $code)
                            <code class="bg-white px-3 py-2 rounded text-sm font-mono text-gray-700 border">{{ $code }}</code>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Disable 2FA Form -->
                    <form action="{{ route('two-factor.disable') }}" method="POST" class="mt-6">
                        @csrf
                        @method('DELETE')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Anda</label>
                            <input type="password" name="password" required
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Masukkan password untuk konfirmasi">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-6 rounded-xl transition-colors">
                            Nonaktifkan Two-Factor Authentication
                        </button>
                    </form>

                    <!-- Regenerate Recovery Codes -->
                    <form action="{{ route('two-factor.recovery-codes') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Anda</label>
                            <input type="password" name="password" required
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Masukkan password untuk konfirmasi">
                        </div>
                        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-3 px-6 rounded-xl transition-colors">
                            Generate Recovery Codes Baru
                        </button>
                    </form>
                @else
                    <!-- 2FA Disabled State -->
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Two-Factor Authentication Belum Aktif</h3>
                            <p class="text-sm text-gray-600 mt-1">Aktifkan untuk perlindungan ekstra terhadap akses tidak sah.</p>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <h3 class="font-semibold text-blue-800 mb-2">Cara Kerja 2FA</h3>
                        <ol class="text-sm text-blue-700 space-y-2 list-decimal list-inside">
                            <li>Install aplikasi authenticator (Google Authenticator, Authy, dll)</li>
                            <li>Scan QR code yang akan ditampilkan</li>
                            <li>Masukkan kode 6 digit dari aplikasi untuk verifikasi</li>
                            <li>Simpan recovery codes di tempat yang aman</li>
                        </ol>
                    </div>

                    <a href="{{ route('two-factor.enable') }}"
                       class="block w-full text-center bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition-colors">
                        Aktifkan Two-Factor Authentication
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
