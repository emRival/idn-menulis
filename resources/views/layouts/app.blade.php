<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- CSRF Token - Required for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary Meta Tags -->
    <title>@yield('title', 'IDN Menulis - Platform Menulis Indonesia')</title>
    <meta name="title" content="@yield('meta_title', 'IDN Menulis - Platform Menulis Indonesia')">
    <meta name="description"
        content="@yield('meta_description', 'Platform menulis dan berbagi cerita terbaik di Indonesia. Tulis, baca, dan bagikan karya tulismu.')">
    <meta name="keywords" content="@yield('meta_keywords', 'menulis, blog, artikel, cerita, indonesia, penulis')">
    <meta name="author" content="IDN Menulis">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('meta_title', 'IDN Menulis')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="IDN Menulis">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('meta_title', 'IDN Menulis')">
    <meta name="twitter:description" content="@yield('meta_description')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.jpg'))">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Preconnect untuk performa -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Compiled Tailwind CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js via CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Swiper.js for Carousel -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>



    <!-- Custom Styles -->
    <style>
        /* Prevent Alpine.js FOUC (flash of unstyled content) */
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Poppins', sans-serif;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #14b8a6;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #0d9488;
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Line clamp utilities */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Swiper custom styles */
        .swiper-pagination-bullet-active {
            background: #14b8a6 !important;
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: #14b8a6 !important;
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 50%, #f97316 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Card hover effects */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Image zoom on hover */
        .img-zoom {
            transition: transform 0.5s ease;
        }

        .group:hover .img-zoom {
            transform: scale(1.05);
        }
    </style>
    @yield('styles')
</head>

<body class="antialiased bg-slate-50 text-gray-900">
    <div id="app" x-data="{ mobileMenu: false }">
        <!-- Navigation -->
        @include('components.navbar')

        <!-- Flash Messages -->
        @if ($message = Session::get('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-8"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed top-20 right-4 bg-primary-500 text-white px-6 py-4 rounded-xl shadow-2xl z-50 flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ $message }}
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-8"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed top-20 right-4 bg-red-500 text-white px-6 py-4 rounded-xl shadow-2xl z-50 flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ $message }}
            </div>
        @endif

        <!-- Main Content -->
        <main class="pt-20">
            @yield('content')
        </main>

        <!-- Footer -->
        @include('components.footer')
    </div>

    <!-- Login Modal - Moved here to avoid stacking context issues -->
    @guest
        @php
            // Check if registration is enabled using the Setting model
            $registrationEnabled = \App\Models\Setting::where('key', 'registration_enabled')->value('value') !== '0';
        @endphp
        <div x-data="{
                                    get isOpen() { return Alpine.store('loginModal') },
                                    set isOpen(value) { Alpine.store('loginModal', value) }
                                }" x-show="$store.loginModal" x-on:keydown.escape.window="$store.loginModal = false"
            x-cloak class="fixed inset-0 z-[9999] overflow-hidden" style="display: none;">

            <!-- Backdrop -->
            <div x-show="$store.loginModal" x-transition:enter="transition duration-300 ease-out"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                @click="$store.loginModal = false"></div>

            <!-- Modal Panel -->
            <div x-show="$store.loginModal" x-transition:enter="transition duration-300 ease-out"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition duration-200 ease-in"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative min-h-screen flex items-center justify-center p-4">

                <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 overflow-hidden">
                    <!-- Close Button -->
                    <button @click="$store.loginModal = false"
                        class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Header -->
                    <div class="text-center mb-8">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-primary-50 text-primary-600 rounded-2xl mb-4 text-3xl">
                            👋
                        </div>
                        <h2 class="text-2xl font-bold font-display text-gray-900">Selamat Datang!</h2>
                        <p class="text-gray-600 mt-2">Masuk untuk mulai menulis dan berinteraksi.</p>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <input type="email" name="email" required autofocus
                                    class="w-full pl-10 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none"
                                    placeholder="nama@email.com">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">Password</label>
                                <a href="{{ route('password.request') }}"
                                    class="text-xs font-medium text-primary-600 hover:text-primary-700">Lupa Password?</a>
                            </div>
                            <div class="relative" x-data="{ show: false }">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                                <input :type="show ? 'text' : 'password'" name="password" required
                                    class="w-full pl-10 pr-10 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none"
                                    placeholder="••••••••">
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember"
                                    class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm text-gray-600">Ingat saya</span>
                            </label>
                            @if($registrationEnabled)
                                <a href="{{ route('register') }}"
                                    class="text-sm font-medium text-primary-600 hover:text-primary-700">Buat akun baru</a>
                            @endif
                        </div>

                        <button type="submit"
                            class="w-full py-3.5 bg-primary-600 text-white font-bold rounded-xl shadow-lg shadow-primary-600/30 hover:bg-primary-700 hover:shadow-primary-600/50 hover:-translate-y-0.5 transition-all duration-300">
                            Masuk Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endguest

    <!-- Security: CSRF Token Setup for AJAX -->
    <script>
        // Initialize Alpine store for login modal
        document.addEventListener('alpine:init', () => {
            Alpine.store('loginModal', false)
        })

        // Setup CSRF token for all AJAX requests
        window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};

        // Axios CSRF setup (if using Axios)
        if (typeof axios !== 'undefined') {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        }

        // Fetch API CSRF wrapper
        window.secureFetch = function (url, options = {}) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            options.headers = {
                ...options.headers,
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            };

            if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
                options.headers['Content-Type'] = 'application/json';
                options.body = JSON.stringify(options.body);
            }

            return fetch(url, options);
        };

        // Frame busting protection
        if (window.top !== window.self) {
            // Only allow framing from same origin
            try {
                if (window.top.location.host !== window.self.location.host) {
                    window.top.location = window.self.location;
                }
            } catch (e) {
                // Cross-origin frame, break out
                window.top.location = window.self.location;
            }
        }

        // Prevent sensitive data in localStorage
        const sensitiveKeys = ['password', 'token', 'secret', 'api_key'];
        const originalSetItem = localStorage.setItem;
        localStorage.setItem = function (key, value) {
            const lowerKey = key.toLowerCase();
            if (sensitiveKeys.some(sensitive => lowerKey.includes(sensitive))) {
                console.warn('Security: Storing sensitive data in localStorage is not recommended');
            }
            return originalSetItem.apply(this, arguments);
        };


    </script>

    @yield('scripts')
    @stack('scripts')
</body>

</html>