@extends('layouts.app')

@section('title', 'Lupa Password - IDN Menulis')

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        .login-page {
            font-family: 'Inter', sans-serif;
            min-height: calc(100vh - 80px);
        }

        /* Fade in animation */
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Input focus effect */
        .input-modern {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-modern:focus {
            box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.15);
        }

        /* Button shine effect */
        .btn-shine {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-shine::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn-shine:hover::before {
            left: 100%;
        }

        .btn-shine:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px -5px rgba(20, 184, 166, 0.45);
        }

        .btn-shine:active {
            transform: translateY(0);
        }

        /* Spinner */
        .spinner {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 2px solid white;
            width: 20px;
            height: 20px;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@section('content')
    <div class="login-page" x-data="{ isSubmitting: false }">
        <!-- Background -->
        <div
            class="min-h-screen bg-gradient-to-br from-gray-50 via-primary-50/30 to-gray-100 flex items-center justify-center py-16 px-4 sm:px-6 relative">
            <!-- Decorative blobs -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-32 -right-32 w-96 h-96 bg-primary-200/30 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-100/40 rounded-full blur-3xl"></div>
            </div>

            <div class="w-full max-w-md relative z-10 fade-in-up">
                <!-- Card -->
                <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/60 border border-gray-100 p-8 sm:p-10">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-primary-50 text-primary-600 rounded-2xl mb-4 text-3xl">
                            🔐
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">Lupa Password?</h1>
                        <p class="text-gray-500 text-sm">Masukkan email Anda dan kami akan mengirimkan link untuk mereset
                            password.</p>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl fade-in-up">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-green-700">{{ session('status') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-red-800">Terjadi kesalahan</p>
                                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" @submit="isSubmitting = true"
                        class="space-y-6">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </div>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="input-modern w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:bg-white @error('email') border-red-400 ring-2 ring-red-500/20 @enderror"
                                    placeholder="nama@email.com" required autofocus>
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" :disabled="isSubmitting"
                            class="btn-shine w-full py-4 px-6 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/25 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-3">
                            <span x-show="!isSubmitting">Kirim Link Reset Password</span>
                            <span x-show="isSubmitting" x-cloak class="flex items-center gap-3">
                                <div class="spinner"></div>
                                Memproses...
                            </span>
                        </button>
                    </form>

                    <div class="mt-8 text-center pt-6 border-t border-gray-100">
                        <p class="text-gray-600 text-sm">
                            Ingat password anda?
                            <a href="{{ route('login') }}"
                                class="font-medium text-primary-600 hover:text-primary-700 hover:underline transition-colors">
                                Masuk disini
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection