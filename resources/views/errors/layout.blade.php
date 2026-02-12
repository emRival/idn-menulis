<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'IDN Menulis') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .error-illustration {
            max-width: 400px;
            width: 100%;
            height: auto;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        <div class="max-w-xl w-full text-center space-y-8">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 group mb-8">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-primary-600 to-teal-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary-600/20 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <span
                    class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-700">
                    IDN <span class="text-primary-600">Menulis</span>
                </span>
            </a>

            <!-- Error Content -->
            <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
                <div class="mb-6 flex justify-center">
                    @yield('image')
                </div>

                <h1 class="text-4xl font-bold text-gray-900 mb-2">@yield('code')</h1>
                <h2 class="text-xl font-semibold text-gray-700 mb-4">@yield('message')</h2>
                <p class="text-gray-500 mb-8">@yield('description')</p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ url()->previous() }}"
                        class="w-full sm:w-auto px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition">
                        Kembali
                    </a>
                    <a href="{{ route('home') }}"
                        class="w-full sm:w-auto px-6 py-3 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700 transition shadow-lg shadow-primary-600/20">
                        Ke Beranda
                    </a>
                </div>
            </div>

            <p class="text-sm text-gray-400">
                &copy; {{ date('Y') }} IDN Menulis. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>