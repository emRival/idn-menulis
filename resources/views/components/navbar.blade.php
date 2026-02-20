<nav class="bg-white/80 backdrop-blur-md fixed top-0 w-full z-50 border-b border-gray-100"
    x-data="{ mobileMenu: false, searchOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div
                        class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center text-white transform group-hover:rotate-6 transition-transform duration-300 shadow-lg shadow-primary-600/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold font-display text-gray-800">
                        IDN <span class="text-primary-600">Menulis</span>
                    </span>
                </a>
            </div>

            <!-- Desktop Menu -->

            <div class="hidden lg:flex items-center gap-8 ml-12">
                <a href="{{ route('home') }}"
                    class="text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-primary-600' : 'text-gray-600 hover:text-primary-600' }}">
                    Beranda
                </a>

                <a href="{{ route('articles.index') }}"
                    class="text-sm font-medium transition-colors {{ request()->routeIs('articles.*') ? 'text-primary-600' : 'text-gray-600 hover:text-primary-600' }}">
                    Artikel
                </a>

                <!-- Categories Dropdown -->
                <div class="relative group" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false"
                        class="flex items-center gap-1 text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors">
                        Kategori
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute top-full left-1/2 -translate-x-1/2 mt-4 w-64 bg-white rounded-2xl shadow-xl border border-gray-100 p-2 transform origin-top z-50"
                        style="display: none;">
                        @foreach (\App\Models\Category::where('is_active', true)->take(5)->get() as $category)
                            <a href="{{ route('categories.show', $category->slug) }}"
                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-primary-50 transition-colors group/item">
                                <span
                                    class="text-xl group-hover/item:scale-110 transition-transform">{{ $category->icon ?? '📁' }}</span>
                                <span
                                    class="text-sm font-medium text-gray-700 group-hover/item:text-primary-600">{{ $category->name }}</span>
                            </a>
                        @endforeach
                        <div class="h-px bg-gray-100 my-1"></div>
                        <a href="{{ route('categories.index') }}"
                            class="flex items-center justify-center gap-2 p-2 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
                            Lihat Semua Kategori
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>



            <!-- Search Bar Desktop -->
            <div class="hidden lg:block w-full max-w-[240px] ml-auto mr-4">
                <form action="{{ route('articles.search') }}" method="GET">
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="q" placeholder="Cari artikel..."
                            class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl leading-5 text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 sm:text-sm transition-all duration-300">
                    </div>
                </form>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-4">
                @auth
                    <!-- Notifications -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                            class="relative p-2 text-gray-400 hover:text-primary-600 transition-colors rounded-xl hover:bg-primary-50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @if (auth()->user()->unreadNotifications->count() > 0)
                                <span
                                    class="absolute top-1.5 right-1.5 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white"></span>
                            @endif
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 origin-top-right"
                            style="display: none;">
                            <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900">Notifikasi</h3>
                                @if (auth()->user()->unreadNotifications->count() > 0)
                                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                                            Tandai semua dibaca
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                @forelse(auth()->user()->notifications()->take(5)->get() as $notification)
                                    <a href="{{ $notification->action_url ?? '#' }}"
                                        class="block px-4 py-3 hover:bg-gray-50 transition-colors {{ $notification->is_read ? 'opacity-70' : '' }}">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $notification->title ?? 'Notifikasi Baru' }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $notification->message ?? '' }}</p>
                                        <p class="text-[10px] text-gray-400 mt-1">
                                            {{ $notification->created_at ? $notification->created_at->diffForHumans() : '' }}</p>
                                    </a>
                                @empty
                                    <div class="px-4 py-6 text-center text-gray-500 text-sm">
                                        Tidak ada notifikasi baru
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="relative ml-2" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                            class="flex items-center gap-2 group p-1 pr-3 rounded-full hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-200">
                            <img class="h-8 w-8 rounded-full object-cover ring-2 ring-gray-100 group-hover:ring-primary-500 transition-all"
                                src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->full_name) . '&background=14b8a6&color=fff' }}"
                                alt="{{ auth()->user()->full_name }}" />
                            <span
                                class="hidden md:block text-sm font-medium text-gray-700 group-hover:text-gray-900">{{ Str::limit(auth()->user()->full_name, 10) }}</span>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-60 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 origin-top-right divide-y divide-gray-100"
                            style="display: none;">

                            <div class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900">Halo, {{ auth()->user()->first_name }}!</p>
                                <p class="text-xs text-gray-500 mt-0.5 truncate">{{ auth()->user()->email }}</p>
                                <span
                                    class="inline-flex mt-2 items-center px-2 py-0.5 rounded text-xs font-medium {{ auth()->user()->isAdmin() ? 'bg-red-100 text-red-800' : 'bg-primary-100 text-primary-800' }}">
                                    {{ auth()->user()->isAdmin() ? 'Administrator' : 'Penulis' }}
                                </span>
                            </div>

                            <div class="py-1">
                                <a href="{{ route('dashboard') }}"
                                    class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary-600 transition-colors">
                                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-primary-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('dashboard.articles') }}"
                                    class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary-600 transition-colors">
                                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-primary-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                    Artikel Saya
                                </a>
                                <a href="{{ route('bookmarks.index') }}"
                                    class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary-600 transition-colors">
                                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-primary-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                    </svg>
                                    Bookmark
                                </a>
                                <a href="{{ route('profile.show') }}"
                                    class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary-600 transition-colors">
                                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-primary-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Profil Saya
                                </a>
                            </div>

                            @if (auth()->user()->isAdmin())
                                <div class="py-1">
                                    <p class="px-4 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Administrator
                                    </p>
                                    <a href="{{ route('approvals.pending') }}"
                                        class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary-600 transition-colors">
                                        <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-primary-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Verifikasi Artikel
                                        @if (\App\Models\Article::pending()->count() > 0)
                                            <span
                                                class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ \App\Models\Article::pending()->count() }}
                                            </span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.settings') }}"
                                        class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary-600 transition-colors">
                                        <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-primary-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Pengaturan Sistem
                                    </a>
                                </div>
                            @endif

                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="group w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-50 hover:text-red-700 transition-colors">
                                        <svg class="mr-3 h-5 w-5 text-red-400 group-hover:text-red-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    @if (!request()->routeIs('login') && !request()->routeIs('register'))
                        <button @click="$store.loginModal = true"
                            class="hidden sm:flex items-center gap-2 px-5 py-2.5 border border-primary-500 text-primary-600 rounded-xl font-medium hover:bg-primary-50 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span>Masuk</span>
                        </button>
                    @endif

                    @if ($registrationEnabled && !request()->routeIs('register') && !request()->routeIs('login'))
                        <a href="{{ route('register') }}"
                            class="hidden sm:flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl font-medium shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 hover:from-primary-600 hover:to-primary-700 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <span>Daftar Sekarang</span>
                        </a>
                    @endif
                @endauth

                <!-- Mobile Menu Button -->
                <button @click="mobileMenu = !mobileMenu"
                    class="lg:hidden p-2 text-gray-600 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-colors">
                    <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileMenu" x-cloak class="w-6 h-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2" class="lg:hidden border-t border-gray-100 bg-white" x-cloak
        style="display: none;">
        <div class="px-4 pt-4 pb-6 space-y-4">
            <!-- Search Mobile -->
            <form action="{{ route('articles.search') }}" method="GET">
                <div class="relative">
                    <input type="text" name="q" placeholder="Cari artikel..."
                        class="w-full py-2.5 pl-10 pr-4 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </form>

            <div class="space-y-1">
                <a href="{{ route('home') }}"
                    class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-colors {{ request()->routeIs('home') ? 'bg-primary-50 text-primary-600' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Beranda
                </a>
                <a href="{{ route('articles.index') }}"
                    class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-xl transition-colors {{ request()->routeIs('articles.*') ? 'bg-primary-50 text-primary-600' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    Artikel
                </a>

            </div>

            @guest
                <div class="pt-4 border-t border-gray-100 flex gap-3">
                    @if(!request()->routeIs('login'))
                        <button @click="$store.loginModal = true; mobileMenu = false"
                            class="flex-1 py-2.5 text-primary-600 font-medium border border-primary-500 rounded-xl">
                            Masuk
                        </button>
                    @endif

                    @if($registrationEnabled && !request()->routeIs('register'))
                        <a href="{{ route('register') }}"
                            class="flex-1 flex justify-center py-2.5 bg-primary-600 text-white font-medium rounded-xl shadow-lg shadow-primary-500/30">
                            Daftar
                        </a>
                    @endif
                </div>
            @endguest
        </div>
    </div>
</nav>
