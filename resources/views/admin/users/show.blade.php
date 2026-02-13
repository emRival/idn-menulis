@extends('layouts.app')

@section('title', 'Detail Pengguna - Admin IDN Menulis')

@section('content')
    <div x-data="userDetailPage()" class="min-h-screen bg-gray-50">
        <!-- Sticky Header -->
        <div class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-4 gap-3">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center gap-2 text-sm mb-1">
                            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700">Users</a>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <span class="text-gray-900 font-medium">Detail</span>
                        </nav>
                        <h1 class="text-xl font-bold text-gray-900">Detail Pengguna</h1>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.users.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 bg-white border border-gray-200 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali
                        </a>
                        <a href="{{ route('admin.users.edit', $user) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                        <button @click="showResetPasswordModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            Reset Password
                        </button>
                        @if($user->is_active)
                            <button @click="showSuspendModal = true"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-xl hover:bg-yellow-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                Suspend
                            </button>
                        @else
                            <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-xl hover:bg-green-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Aktifkan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert -->
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-green-800">{{ session('success') }}</p>
                    <button @click="$el.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="lg:grid lg:grid-cols-12 lg:gap-6">
                <!-- Left Sidebar - Profile Card -->
                <div class="lg:col-span-4 xl:col-span-3 space-y-6 mb-6 lg:mb-0">
                    <!-- Profile Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <!-- Cover -->
                        <div class="h-24 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-600 relative">
                            <div class="absolute -bottom-12 left-1/2 -translate-x-1/2">
                                <img src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name) . '&background=3b82f6&color=fff&size=96' }}"
                                    alt="{{ $user->full_name }}"
                                    class="w-24 h-24 rounded-2xl border-4 border-white shadow-lg object-cover">
                            </div>
                        </div>

                        <div class="pt-14 pb-6 px-6 text-center">
                            <h2 class="text-xl font-bold text-gray-900">{{ $user->full_name }}</h2>
                            <p class="text-gray-500 text-sm">{{ '@' . $user->username }}</p>

                            <!-- Role Badge -->
                            <div class="flex justify-center gap-2 mt-3">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium
                                    @if($user->role === 'admin') bg-gradient-to-r from-red-500 to-rose-500 text-white
                                    @elseif($user->role === 'guru') bg-gradient-to-r from-blue-500 to-indigo-500 text-white
                                    @else bg-gradient-to-r from-emerald-500 to-teal-500 text-white
                                    @endif">
                                    @if($user->role === 'admin')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    @elseif($user->role === 'guru')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>

                            <!-- Status Badge -->
                            <div class="flex justify-center mt-2">
                                @if(!$user->email_verified_at)
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                                        Pending Verifikasi
                                    </span>
                                @elseif($user->is_active)
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        Nonaktif / Suspended
                                    </span>
                                @endif
                            </div>

                            <!-- Contact Info -->
                            <div class="mt-6 space-y-3 text-left">
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                    <div
                                        class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs text-gray-500">Email</p>
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $user->email }}</p>
                                    </div>
                                </div>

                                @if($user->class)
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                        <div
                                            class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Kelas / Jabatan</p>
                                            <p class="text-sm font-medium text-gray-900">{{ $user->class }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($user->school_name)
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                        <div
                                            class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Sekolah</p>
                                            <p class="text-sm font-medium text-gray-900">{{ $user->school_name }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Quick Actions -->
                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Quick Actions</p>
                                <div class="space-y-2">
                                    <button @click="showChangeRoleModal = true"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-left text-sm text-gray-700 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Ubah Role
                                    </button>
                                    <button @click="showNotificationModal = true"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-left text-sm text-gray-700 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                        Kirim Notifikasi
                                    </button>
                                    <button @click="showDeleteModal = true"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-left text-sm text-red-600 bg-red-50 rounded-xl hover:bg-red-100 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus Akun
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Mini Cards -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm text-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $articleStats['total'] }}</p>
                            <p class="text-xs text-gray-500">Artikel</p>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm text-center">
                            <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($articleStats['total_views']) }}
                            </p>
                            <p class="text-xs text-gray-500">Views</p>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm text-center">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $user->articles->sum(fn($a) => $a->likedBy()->count()) }}</p>
                            <p class="text-xs text-gray-500">Likes</p>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm text-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $user->comments->count() }}</p>
                            <p class="text-xs text-gray-500">Komentar</p>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-8 xl:col-span-9">
                    <!-- Info Cards Row -->
                    <div class="grid md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Published</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $articleStats['published'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Pending Review</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $articleStats['pending'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Draft</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $articleStats['draft'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <!-- Tab Navigation -->
                        <div class="border-b border-gray-100">
                            <nav class="flex overflow-x-auto scrollbar-hide">
                                <button @click="activeTab = 'profile'"
                                    :class="{ 'border-blue-500 text-blue-600 bg-blue-50/50': activeTab === 'profile', 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'profile' }"
                                    class="flex items-center gap-2 px-5 py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Profil
                                </button>
                                <button @click="activeTab = 'articles'"
                                    :class="{ 'border-blue-500 text-blue-600 bg-blue-50/50': activeTab === 'articles', 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'articles' }"
                                    class="flex items-center gap-2 px-5 py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Artikel
                                    <span
                                        class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">{{ $articleStats['total'] }}</span>
                                </button>
                                <button @click="activeTab = 'comments'"
                                    :class="{ 'border-blue-500 text-blue-600 bg-blue-50/50': activeTab === 'comments', 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'comments' }"
                                    class="flex items-center gap-2 px-5 py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Komentar
                                    <span
                                        class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">{{ $user->comments->count() }}</span>
                                </button>
                                <button @click="activeTab = 'activity'"
                                    :class="{ 'border-blue-500 text-blue-600 bg-blue-50/50': activeTab === 'activity', 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'activity' }"
                                    class="flex items-center gap-2 px-5 py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Aktivitas
                                </button>
                                <button @click="activeTab = 'violations'"
                                    :class="{ 'border-blue-500 text-blue-600 bg-blue-50/50': activeTab === 'violations', 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'violations' }"
                                    class="flex items-center gap-2 px-5 py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Pelanggaran
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div class="p-6">
                            <!-- Profile Tab -->
                            <div x-show="activeTab === 'profile'" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                <div class="grid md:grid-cols-2 gap-6">
                                    <!-- Info Utama -->
                                    <div>
                                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Informasi Utama
                                        </h3>
                                        <div class="bg-gray-50 rounded-xl overflow-hidden">
                                            <table class="w-full text-sm">
                                                <tbody class="divide-y divide-gray-100">
                                                    <tr>
                                                        <td class="px-4 py-3 text-gray-500 font-medium">Username</td>
                                                        <td class="px-4 py-3 text-gray-900">{{ $user->username }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-3 text-gray-500 font-medium">Email</td>
                                                        <td class="px-4 py-3 text-gray-900">{{ $user->email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-3 text-gray-500 font-medium">Nama Lengkap</td>
                                                        <td class="px-4 py-3 text-gray-900">{{ $user->full_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-3 text-gray-500 font-medium">Role</td>
                                                        <td class="px-4 py-3">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                @if($user->role === 'admin') bg-red-100 text-red-700
                                                                @elseif($user->role === 'guru') bg-blue-100 text-blue-700
                                                                @else bg-emerald-100 text-emerald-700
                                                                @endif">
                                                                {{ ucfirst($user->role) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-3 text-gray-500 font-medium">Kelas</td>
                                                        <td class="px-4 py-3 text-gray-900">{{ $user->class ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-3 text-gray-500 font-medium">Sekolah</td>
                                                        <td class="px-4 py-3 text-gray-900">{{ $user->school_name ?? '-' }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Timeline -->
                                    <div>
                                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Timeline
                                        </h3>
                                        <div class="bg-gray-50 rounded-xl overflow-hidden">
                                            <table class="w-full text-sm">
                                                <tbody class="divide-y divide-gray-100">
                                                    <tr>
                                                        <td class="px-4 py-3 text-gray-500 font-medium">Bergabung</td>
                                                        <td class="px-4 py-3 text-gray-900">
                                                            {{ $user->created_at->format('d M Y, H:i') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-3 text-gray-500 font-medium">Email Verifikasi
                                                        </td>
                                                        <td class="px-4 py-3 text-gray-900">
                                                            @if($user->email_verified_at)
                                                                {{ $user->email_verified_at->format('d M Y, H:i') }}
                                                            @else
                                                                <span class="text-yellow-600">Belum verifikasi</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-3 text-gray-500 font-medium">Terakhir Login</td>
                                                        <td class="px-4 py-3 text-gray-900">
                                                            @if($user->last_login_at)
                                                                <span title="{{ $user->last_login_at->format('d M Y, H:i') }}">
                                                                    {{ $user->last_login_at->diffForHumans() }}
                                                                </span>
                                                            @else
                                                                <span class="text-gray-400">Belum pernah</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-3 text-gray-500 font-medium">Update Terakhir</td>
                                                        <td class="px-4 py-3 text-gray-900">
                                                            {{ $user->updated_at->format('d M Y, H:i') }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Bio -->
                                    <div class="md:col-span-2">
                                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 6h16M4 12h16M4 18h7" />
                                            </svg>
                                            Bio
                                        </h3>
                                        <div class="bg-gray-50 rounded-xl p-4">
                                            @if($user->bio)
                                                <p class="text-gray-700">{{ $user->bio }}</p>
                                            @else
                                                <p class="text-gray-400 italic">Belum ada bio.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Articles Tab -->
                            <div x-show="activeTab === 'articles'" x-cloak
                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100">
                                @if($user->articles->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="w-full">
                                            <thead>
                                                <tr class="text-left border-b border-gray-100">
                                                    <th
                                                        class="pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                                        Artikel</th>
                                                    <th
                                                        class="pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                                        Status</th>
                                                    <th
                                                        class="pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                                                        Views</th>
                                                    <th
                                                        class="pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                                                        Likes</th>
                                                    <th
                                                        class="pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                                        Tanggal</th>
                                                    <th
                                                        class="pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">
                                                        Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-50">
                                                @foreach($user->articles as $article)
                                                    <tr class="hover:bg-gray-50/50">
                                                        <td class="py-4">
                                                            <div class="flex items-center gap-3">
                                                                @if($article->featured_image)
                                                                    <img src="{{ Storage::url($article->featured_image) }}"
                                                                        alt="{{ $article->title }}"
                                                                        class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                                                                @else
                                                                    <div
                                                                        class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                                                        <svg class="w-6 h-6 text-white/70" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                        </svg>
                                                                    </div>
                                                                @endif
                                                                <div class="min-w-0">
                                                                    <p class="font-medium text-gray-900 line-clamp-1">
                                                                        {{ $article->title }}</p>
                                                                    <p class="text-xs text-gray-500">
                                                                        {{ $article->category?->name ?? 'Tanpa Kategori' }}</p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="py-4">
                                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                                                                @if($article->status === 'published') bg-green-100 text-green-700
                                                                @elseif($article->status === 'pending_review') bg-yellow-100 text-yellow-700
                                                                @elseif($article->status === 'rejected') bg-red-100 text-red-700
                                                                @else bg-gray-100 text-gray-700
                                                                @endif">
                                                                @if($article->status === 'published')
                                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                                @elseif($article->status === 'pending_review')
                                                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                                                @endif
                                                                {{ ucfirst(str_replace('_', ' ', $article->status)) }}
                                                            </span>
                                                        </td>
                                                        <td class="py-4 text-center text-sm text-gray-600">
                                                            {{ number_format($article->views_count) }}</td>
                                                        <td class="py-4 text-center text-sm text-gray-600">
                                                            {{ $article->likedBy()->count() }}</td>
                                                        <td class="py-4 text-sm text-gray-500">
                                                            {{ $article->created_at->format('d M Y') }}</td>
                                                        <td class="py-4 text-right">
                                                            <div class="flex items-center justify-end gap-1">
                                                                <a href="{{ route('articles.show', $article->slug) }}"
                                                                    target="_blank"
                                                                    class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                                    title="Preview">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                    </svg>
                                                                </a>
                                                                <a href="{{ route('articles.edit', $article->slug) }}"
                                                                    class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                                                    title="Edit">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-16">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Belum ada artikel yang ditulis</p>
                                        <p class="text-sm text-gray-400 mt-1">Pengguna ini belum menulis artikel apapun.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Comments Tab -->
                            <div x-show="activeTab === 'comments'" x-cloak
                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100">
                                @if($user->comments->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($user->comments as $comment)
                                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                                                <p class="text-gray-700 text-sm leading-relaxed">{{ $comment->content }}</p>
                                                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                                                    <div class="flex items-center gap-3 text-xs text-gray-500">
                                                        @if($comment->article)
                                                            <a href="{{ route('articles.show', $comment->article->slug) }}"
                                                                class="inline-flex items-center gap-1 text-blue-600 hover:underline">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                                </svg>
                                                                {{ Str::limit($comment->article->title, 35) }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                    <span
                                                        class="text-xs text-gray-400">{{ $comment->created_at->format('d M Y H:i') }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-16">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Belum ada komentar</p>
                                        <p class="text-sm text-gray-400 mt-1">Pengguna ini belum membuat komentar apapun.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Activity Tab -->
                            <div x-show="activeTab === 'activity'" x-cloak
                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100">
                                @if($user->activityLogs->count() > 0)
                                    <div class="relative">
                                        <!-- Timeline Line -->
                                        <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                                        <div class="space-y-6">
                                            @foreach($user->activityLogs as $log)
                                                <div class="relative flex gap-4 pl-2">
                                                    <!-- Icon -->
                                                    <div class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                                        @if(str_contains($log->action, 'login')) bg-green-500 text-white
                                                        @elseif(str_contains($log->action, 'publish') || str_contains($log->action, 'create')) bg-blue-500 text-white
                                                        @elseif(str_contains($log->action, 'edit') || str_contains($log->action, 'update')) bg-yellow-500 text-white
                                                        @elseif(str_contains($log->action, 'delete')) bg-red-500 text-white
                                                        @elseif(str_contains($log->action, 'comment')) bg-purple-500 text-white
                                                        @else bg-gray-400 text-white
                                                        @endif">
                                                        @if(str_contains($log->action, 'login'))
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                                            </svg>
                                                        @elseif(str_contains($log->action, 'publish') || str_contains($log->action, 'create'))
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                            </svg>
                                                        @elseif(str_contains($log->action, 'edit') || str_contains($log->action, 'update'))
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        @elseif(str_contains($log->action, 'delete'))
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        @else
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        @endif
                                                    </div>

                                                    <!-- Content -->
                                                    <div class="flex-1 bg-gray-50 rounded-xl p-4 border border-gray-100">
                                                        <div class="flex items-start justify-between gap-4">
                                                            <div>
                                                                <p class="font-medium text-gray-900 text-sm">
                                                                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}</p>
                                                                <p class="text-sm text-gray-600 mt-1">{{ $log->description }}</p>
                                                            </div>
                                                            <span
                                                                class="text-xs text-gray-400 whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</span>
                                                        </div>
                                                        @if($log->ip_address)
                                                            <div class="mt-2 pt-2 border-t border-gray-100">
                                                                <span class="text-xs text-gray-400">IP: {{ $log->ip_address }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-16">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Belum ada riwayat aktivitas</p>
                                        <p class="text-sm text-gray-400 mt-1">Aktivitas pengguna akan muncul di sini.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Violations Tab -->
                            <div x-show="activeTab === 'violations'" x-cloak
                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100">
                                <div class="text-center py-16">
                                    <div
                                        class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">Tidak ada pelanggaran</p>
                                    <p class="text-sm text-gray-400 mt-1">Pengguna ini tidak memiliki riwayat pelanggaran.
                                    </p>

                                    <!-- Add Warning Button -->
                                    <button
                                        class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-xl hover:bg-yellow-200 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        Tambah Warning
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reset Password Modal -->
        <div x-show="showResetPasswordModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showResetPasswordModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="showResetPasswordModal = false"
                    class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="showResetPasswordModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Reset Password</h3>
                                    <p class="text-sm text-gray-500">Untuk: {{ $user->full_name }}</p>
                                </div>
                            </div>
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru (kosongkan untuk
                                    random)</label>
                                <input type="password" name="new_password"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                                    placeholder="Masukkan password baru...">
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                            <button type="button" @click="showResetPasswordModal = false"
                                class="px-4 py-2.5 text-gray-700 font-medium rounded-xl hover:bg-gray-100 transition-colors">Batal</button>
                            <button type="submit"
                                class="px-4 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-colors">Reset
                                Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Role Modal -->
        <div x-show="showChangeRoleModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showChangeRoleModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="showChangeRoleModal = false"
                    class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="showChangeRoleModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('admin.users.change-role', $user) }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Ubah Role</h3>
                                    <p class="text-sm text-gray-500">Untuk: {{ $user->full_name }}</p>
                                </div>
                            </div>
                            <div class="mt-6 space-y-3">
                                <label
                                    class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input type="radio" name="role" value="admin" {{ $user->role === 'admin' ? 'checked' : '' }} class="w-4 h-4 text-red-600">
                                    <div class="flex items-center gap-2">
                                        <span class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                        <div>
                                            <p class="font-medium text-gray-900">Admin</p>
                                            <p class="text-xs text-gray-500">Akses penuh ke semua fitur</p>
                                        </div>
                                    </div>
                                </label>
                                <label
                                    class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input type="radio" name="role" value="guru" {{ $user->role === 'guru' ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600">
                                    <div class="flex items-center gap-2">
                                        <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z" />
                                            </svg>
                                        </span>
                                        <div>
                                            <p class="font-medium text-gray-900">Guru</p>
                                            <p class="text-xs text-gray-500">Review & moderasi artikel</p>
                                        </div>
                                    </div>
                                </label>
                                <label
                                    class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input type="radio" name="role" value="siswa" {{ $user->role === 'siswa' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600">
                                    <div class="flex items-center gap-2">
                                        <span class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                        <div>
                                            <p class="font-medium text-gray-900">Siswa</p>
                                            <p class="text-xs text-gray-500">Menulis & membaca artikel</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                            <button type="button" @click="showChangeRoleModal = false"
                                class="px-4 py-2.5 text-gray-700 font-medium rounded-xl hover:bg-gray-100 transition-colors">Batal</button>
                            <button type="submit"
                                class="px-4 py-2.5 bg-purple-600 text-white font-medium rounded-xl hover:bg-purple-700 transition-colors">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Suspend Modal -->
        <div x-show="showSuspendModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showSuspendModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="showSuspendModal = false"
                    class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="showSuspendModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('admin.users.deactivate', $user) }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Suspend Pengguna</h3>
                                    <p class="text-sm text-gray-500">{{ $user->full_name }} akan dinonaktifkan</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-gray-600 text-sm">Pengguna yang disuspend tidak akan dapat login dan
                                    mengakses platform. Anda dapat mengaktifkan kembali akun ini kapan saja.</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                            <button type="button" @click="showSuspendModal = false"
                                class="px-4 py-2.5 text-gray-700 font-medium rounded-xl hover:bg-gray-100 transition-colors">Batal</button>
                            <button type="submit"
                                class="px-4 py-2.5 bg-yellow-500 text-white font-medium rounded-xl hover:bg-yellow-600 transition-colors">Ya,
                                Suspend</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="showDeleteModal = false"
                    class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Hapus Akun</h3>
                                <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-gray-600 text-sm">Anda yakin ingin menghapus akun <span
                                    class="font-semibold text-gray-900">{{ $user->full_name }}</span>? Semua data terkait
                                termasuk artikel dan komentar akan dihapus secara permanen.</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" @click="showDeleteModal = false"
                            class="px-4 py-2.5 text-gray-700 font-medium rounded-xl hover:bg-gray-100 transition-colors">Batal</button>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2.5 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition-colors">Ya,
                                Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Modal -->
        <div x-show="showNotificationModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showNotificationModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="showNotificationModal = false"
                    class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="showNotificationModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('admin.users.notify', $user) }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Kirim Notifikasi</h3>
                                    <p class="text-sm text-gray-500">Kepada: {{ $user->full_name }}</p>
                                </div>
                            </div>
                            <div class="mt-6 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                                    <input type="text" name="title" required
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                        placeholder="Judul notifikasi...">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                                    <textarea name="message" rows="3" required
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 resize-none"
                                        placeholder="Isi pesan..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                            <button type="button" @click="showNotificationModal = false"
                                class="px-4 py-2.5 text-gray-700 font-medium rounded-xl hover:bg-gray-100 transition-colors">Batal</button>
                            <button type="submit"
                                class="px-4 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function userDetailPage() {
            return {
                activeTab: 'profile',
                showResetPasswordModal: false,
                showChangeRoleModal: false,
                showSuspendModal: false,
                showDeleteModal: false,
                showNotificationModal: false
            }
        }
    </script>
@endsection