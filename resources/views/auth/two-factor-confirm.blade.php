@extends('layouts.app')

@section('title', 'Konfirmasi Two-Factor Authentication - IDN Menulis')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-primary-50/30 to-accent-50/20 py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-8 border-b border-gray-100">
                <h1 class="text-2xl font-bold text-gray-900 font-display">Setup Two-Factor Authentication</h1>
                <p class="mt-2 text-gray-600">Ikuti langkah-langkah berikut untuk mengaktifkan 2FA</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Step 1: Scan QR Code -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="bg-primary-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-sm">1</span>
                        Scan QR Code
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">Buka aplikasi authenticator Anda dan scan QR code di bawah ini:</p>

                    <div class="flex justify-center mb-4">
                        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="w-48 h-48 border rounded-lg">
                    </div>

                    <p class="text-sm text-gray-600 text-center">Atau masukkan kode ini secara manual:</p>
                    <code class="block text-center bg-white px-4 py-2 rounded-lg border mt-2 font-mono text-sm break-all">{{ $secret }}</code>
                </div>

                <!-- Step 2: Enter Code -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="bg-primary-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-sm">2</span>
                        Verifikasi Kode
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">Masukkan kode 6 digit dari aplikasi authenticator untuk mengkonfirmasi:</p>

                    <form action="{{ route('two-factor.confirm') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <input type="text" name="code" maxlength="6" required autofocus
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent text-center text-2xl tracking-widest font-mono"
                                   placeholder="000000"
                                   pattern="[0-9]{6}"
                                   inputmode="numeric">
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition-colors">
                            Konfirmasi & Aktifkan 2FA
                        </button>
                    </form>
                </div>

                <!-- Step 3: Save Recovery Codes -->
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-6">
                    <h3 class="font-semibold text-amber-800 mb-4 flex items-center gap-2">
                        <span class="bg-amber-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-sm">3</span>
                        Simpan Recovery Codes
                    </h3>
                    <p class="text-sm text-amber-700 mb-4">
                        <strong>PENTING:</strong> Simpan kode-kode ini di tempat yang aman sebelum melanjutkan.
                        Anda akan membutuhkan salah satu kode ini jika kehilangan akses ke aplikasi authenticator.
                    </p>

                    <div class="bg-white rounded-lg p-4 border border-amber-200">
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($recoveryCodes as $code)
                            <code class="bg-amber-50 px-3 py-2 rounded text-sm font-mono text-gray-700">{{ $code }}</code>
                            @endforeach
                        </div>
                    </div>

                    <button onclick="copyRecoveryCodes()" class="mt-4 w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors text-sm">
                        📋 Salin Recovery Codes
                    </button>
                </div>

                <a href="{{ route('two-factor.index') }}" class="block text-center text-gray-600 hover:text-gray-800">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyRecoveryCodes() {
    const codes = @json($recoveryCodes);
    const text = codes.join('\n');
    navigator.clipboard.writeText(text).then(() => {
        alert('Recovery codes berhasil disalin!');
    });
}
</script>
@endsection
