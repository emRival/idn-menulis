@extends('layouts.app')

@section('title', 'Pengaturan - Admin IDN Menulis')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Pengaturan</h1>
            <p class="text-gray-600 mt-1">Konfigurasi platform IDN Menulis</p>
        </div>

        <!-- Toast Notification -->
        <div x-data="{ show: false, message: '', success: true }"
            x-on:toast.window="show = true; message = $event.detail.message; success = $event.detail.success; setTimeout(() => show = false, 3000)"
            x-show="show" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-lg border flex items-center gap-3"
            :class="success ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'">
            <svg x-show="success" class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            <svg x-show="!success" class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd" />
            </svg>
            <span x-text="message" class="text-sm font-medium"></span>
        </div>

        <div class="space-y-6">
            <!-- Registration Settings -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6" x-data="registrationToggle()">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Pendaftaran Pengguna
                </h2>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div>
                        <p class="font-medium text-gray-900">Buka Pendaftaran</p>
                        <p class="text-sm text-gray-500 mt-0.5">Izinkan pengguna baru untuk mendaftar akun</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium" :class="enabled ? 'text-green-600' : 'text-gray-400'"
                            x-text="enabled ? 'Aktif' : 'Nonaktif'"></span>
                        <button @click="toggle()" :disabled="loading"
                            class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50"
                            :class="enabled ? 'bg-primary-500' : 'bg-gray-300'">
                            <span
                                class="inline-block h-5 w-5 transform rounded-full bg-white shadow-sm transition-transform duration-300"
                                :class="enabled ? 'translate-x-6' : 'translate-x-1'"></span>
                        </button>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Jika dinonaktifkan, halaman /register tidak bisa diakses oleh siapapun.
                </p>
            </div>

            <!-- General Settings -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Pengaturan Umum
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Platform</label>
                        <input type="text" value="IDN Menulis" disabled
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea disabled rows="2"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-600">Platform literasi digital untuk siswa Indonesia</textarea>
                    </div>
                </div>
            </div>

            <!-- Article Settings -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Pengaturan Artikel
                </h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div>
                            <p class="font-medium text-gray-900">Perlu Persetujuan Guru</p>
                            <p class="text-sm text-gray-500">Artikel siswa harus disetujui guru sebelum dipublikasikan</p>
                        </div>
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg text-sm font-medium">Aktif</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div>
                            <p class="font-medium text-gray-900">Moderasi Komentar</p>
                            <p class="text-sm text-gray-500">Komentar harus dimoderasi sebelum ditampilkan</p>
                        </div>
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg text-sm font-medium">Aktif</span>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Informasi Sistem
                </h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <p class="text-gray-500 text-xs mb-1">Laravel Version</p>
                        <p class="text-gray-900 font-medium">{{ app()->version() }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <p class="text-gray-500 text-xs mb-1">PHP Version</p>
                        <p class="text-gray-900 font-medium">{{ phpversion() }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <p class="text-gray-500 text-xs mb-1">Environment</p>
                        <p class="text-gray-900 font-medium">{{ app()->environment() }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <p class="text-gray-500 text-xs mb-1">Debug Mode</p>
                        <p class="text-gray-900 font-medium">{{ config('app.debug') ? 'On' : 'Off' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function registrationToggle() {
            return {
                enabled: @json($registrationEnabled),
                loading: false,

                async toggle() {
                    this.loading = true;
                    const newState = !this.enabled;

                    try {
                        const response = await fetch('{{ route("admin.settings.toggle-registration") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ enabled: newState })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.enabled = newState;
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, success: true } }));
                        } else {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Gagal mengubah pengaturan.', success: false } }));
                        }
                    } catch (error) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Terjadi kesalahan.', success: false } }));
                    }

                    this.loading = false;
                }
            }
        }
    </script>
@endsection