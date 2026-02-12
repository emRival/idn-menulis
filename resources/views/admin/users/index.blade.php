@extends('layouts.app')

@section('title', 'Manajemen Pengguna - Admin IDN Menulis')

@section('content')
    <div x-data="userManagement()"
        @open-reset-password-modal.window="showResetPasswordModal($event.detail.userId, $event.detail.userName)"
        @open-change-role-modal.window="showChangeRoleModal($event.detail.userId, $event.detail.userName, $event.detail.currentRole)"
        @open-delete-modal.window="showDeleteModal($event.detail.userId, $event.detail.userName)"
        class="min-h-screen bg-gray-50">
        <!-- Sticky Header -->
        <div class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-4 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                            <span
                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </span>
                            Manajemen Pengguna
                        </h1>
                        <p class="text-sm text-gray-500 mt-1 ml-13">Kelola akun & monitoring aktivitas pengguna</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.users.create') }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/25">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Tambah User
                        </a>
                        <a href="{{ route('admin.users.export', request()->query()) }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export Data
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                            <p class="text-sm text-gray-500">Total Pengguna</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active']) }}</p>
                            <p class="text-sm text-gray-500">Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending']) }}</p>
                            <p class="text-sm text-gray-500">Pending</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['inactive']) }}</p>
                            <p class="text-sm text-gray-500">Nonaktif</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('reset_passwords'))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                    <h4 class="font-semibold text-blue-800 mb-2">Password Baru:</h4>
                    <div class="space-y-1 text-sm">
                        @foreach(session('reset_passwords') as $username => $password)
                            <p class="text-blue-700"><span class="font-medium">{{ $username }}:</span> <code
                                    class="bg-blue-100 px-2 py-0.5 rounded">{{ $password }}</code></p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
                <form action="{{ route('admin.users.index') }}" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                        <!-- Search -->
                        <div class="lg:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Cari Pengguna</label>
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm"
                                    placeholder="Nama, email, atau username...">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Role Filter -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Role</label>
                            <select name="role"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm">
                                <option value="">Semua Role</option>
                                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                                <option value="guru" @selected(request('role') === 'guru')>Guru</option>
                                <option value="siswa" @selected(request('role') === 'siswa')>Siswa</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Status</label>
                            <select name="status"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm">
                                <option value="">Semua Status</option>
                                <option value="1" @selected(request('status') === '1')>Aktif</option>
                                <option value="0" @selected(request('status') === '0')>Nonaktif</option>
                                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            </select>
                        </div>

                        <!-- Class Filter -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Kelas</label>
                            <select name="class"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm">
                                <option value="">Semua Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class }}" @selected(request('class') === $class)>{{ $class }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-end gap-2">
                            <button type="submit"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filter
                            </button>
                            <a href="{{ route('admin.users.index') }}"
                                class="px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">
                                Reset
                            </a>
                        </div>
                    </div>

                    <!-- Date Filters (Collapsible) -->
                    <div x-data="{ open: {{ request('date_from') || request('date_to') ? 'true' : 'false' }} }"
                        class="mt-4">
                        <button type="button" @click="open = !open"
                            class="text-sm text-blue-600 hover:text-blue-700 flex items-center gap-1">
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            Filter Lanjutan
                        </button>
                        <div x-show="open" x-collapse class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">Tanggal Daftar Dari</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">Tanggal Daftar Sampai</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Bulk Actions -->
            <div x-show="selectedUsers.length > 0" x-transition
                class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-6 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <span class="text-blue-600 font-bold" x-text="selectedUsers.length"></span>
                    </div>
                    <p class="text-blue-800 font-medium">pengguna dipilih</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button @click="bulkAction('activate')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Aktifkan
                    </button>
                    <button @click="bulkAction('deactivate')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Nonaktifkan
                    </button>
                    <button @click="bulkAction('reset_password')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        Reset Password
                    </button>
                    <button @click="bulkAction('delete')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus
                    </button>
                    <button @click="clearSelection()" class="px-3 py-2 text-gray-600 text-sm hover:text-gray-800">
                        Batal
                    </button>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-4 py-4 text-left">
                                    <input type="checkbox" @change="toggleSelectAll($event)"
                                        :checked="selectedUsers.length === {{ $users->count() }}"
                                        class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                </th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Pengguna</th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Role</th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Kelas</th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Artikel</th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Terakhir Login</th>
                                <th
                                    class="px-4 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50/50 transition-colors"
                                    :class="{ 'bg-blue-50/50': selectedUsers.includes({{ $user->id }}) }">
                                    <td class="px-4 py-4">
                                        <input type="checkbox" value="{{ $user->id }}" @change="toggleUser({{ $user->id }})"
                                            :checked="selectedUsers.includes({{ $user->id }})"
                                            class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name) . '&background=3b82f6&color=fff&size=40' }}"
                                                alt="{{ $user->full_name }}"
                                                class="w-10 h-10 rounded-xl object-cover flex-shrink-0">
                                            <div class="min-w-0">
                                                <p class="font-medium text-gray-900 truncate">{{ $user->full_name }}</p>
                                                <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium
                                            @if($user->role === 'admin') bg-red-100 text-red-700
                                            @elseif($user->role === 'guru') bg-blue-100 text-blue-700
                                            @else bg-emerald-100 text-emerald-700
                                            @endif">
                                            @if($user->role === 'admin')
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @elseif($user->role === 'guru')
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                                                </svg>
                                            @else
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600">{{ $user->class ?? '-' }}</td>
                                    <td class="px-4 py-4">
                                        @if(!$user->email_verified_at)
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                                Pending
                                            </span>
                                        @elseif($user->is_active)
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span
                                            class="inline-flex items-center justify-center min-w-[2rem] px-2 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg">
                                            {{ $user->articles_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500">
                                        @if($user->last_login_at)
                                            <span
                                                title="{{ $user->last_login_at->format('d M Y H:i') }}">{{ $user->last_login_at->diffForHumans() }}</span>
                                        @else
                                            <span class="text-gray-400">Belum pernah</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.users.show', $user) }}"
                                                class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Lihat Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                                class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            <!-- Action Dropdown -->
                                            <div x-data="{ open: false }" class="relative">
                                                <button @click="open = !open" type="button"
                                                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                    </svg>
                                                </button>

                                                <div x-show="open" @click.away="open = false" x-transition
                                                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                                                    <!-- Reset Password -->
                                                    <button type="button"
                                                        @click="open = false; $dispatch('open-reset-password-modal', { userId: {{ $user->id }}, userName: '{{ addslashes($user->full_name) }}' })"
                                                        class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                        </svg>
                                                        Reset Password
                                                    </button>

                                                    <!-- Change Role -->
                                                    <button type="button"
                                                        @click="open = false; $dispatch('open-change-role-modal', { userId: {{ $user->id }}, userName: '{{ addslashes($user->full_name) }}', currentRole: '{{ $user->role }}' })"
                                                        class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                        Ubah Role
                                                    </button>

                                                    <hr class="my-1 border-gray-100">

                                                    @if($user->is_active)
                                                        <form action="{{ route('admin.users.deactivate', $user) }}" method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                class="w-full flex items-center gap-2 px-4 py-2 text-sm text-yellow-600 hover:bg-yellow-50">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                                </svg>
                                                                Nonaktifkan
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('admin.users.activate', $user) }}" method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                class="w-full flex items-center gap-2 px-4 py-2 text-sm text-green-600 hover:bg-green-50">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                Aktifkan
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <hr class="my-1 border-gray-100">

                                                    <button type="button"
                                                        @click="open = false; $dispatch('open-delete-modal', { userId: {{ $user->id }}, userName: '{{ addslashes($user->full_name) }}' })"
                                                        class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-16 text-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Tidak ada pengguna ditemukan</p>
                                        <p class="text-sm text-gray-400 mt-1">Coba ubah filter pencarian Anda</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Reset Password Modal -->
        <div x-show="resetPasswordModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="resetPasswordModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="resetPasswordModal = false"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="resetPasswordModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="'/admin/users/' + selectedUserId + '/reset-password'" method="POST">
                        @csrf
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Reset Password</h3>
                                    <p class="text-sm text-gray-500">Untuk pengguna: <span class="font-medium"
                                            x-text="selectedUserName"></span></p>
                                </div>
                            </div>
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru (kosongkan untuk
                                    random)</label>
                                <input type="password" name="new_password"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                    placeholder="Masukkan password baru...">
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                            <button type="button" @click="resetPasswordModal = false"
                                class="px-4 py-2.5 text-gray-700 font-medium rounded-xl hover:bg-gray-100 transition-colors">Batal</button>
                            <button type="submit"
                                class="px-4 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors">Reset
                                Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Role Modal -->
        <div x-show="changeRoleModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="changeRoleModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="changeRoleModal = false"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="changeRoleModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="'/admin/users/' + selectedUserId + '/ubah-role'" method="POST">
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
                                    <p class="text-sm text-gray-500">Untuk pengguna: <span class="font-medium"
                                            x-text="selectedUserName"></span></p>
                                </div>
                            </div>
                            <div class="mt-6 space-y-3">
                                <label
                                    class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors"
                                    :class="{ 'border-red-400 bg-red-50': selectedRole === 'admin' }">
                                    <input type="radio" name="role" value="admin" x-model="selectedRole"
                                        class="w-4 h-4 text-red-600">
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
                                    class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors"
                                    :class="{ 'border-blue-400 bg-blue-50': selectedRole === 'guru' }">
                                    <input type="radio" name="role" value="guru" x-model="selectedRole"
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
                                    class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors"
                                    :class="{ 'border-emerald-400 bg-emerald-50': selectedRole === 'siswa' }">
                                    <input type="radio" name="role" value="siswa" x-model="selectedRole"
                                        class="w-4 h-4 text-emerald-600">
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
                            <button type="button" @click="changeRoleModal = false"
                                class="px-4 py-2.5 text-gray-700 font-medium rounded-xl hover:bg-gray-100 transition-colors">Batal</button>
                            <button type="submit"
                                class="px-4 py-2.5 bg-purple-600 text-white font-medium rounded-xl hover:bg-purple-700 transition-colors">Simpan
                                Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="deleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="deleteModal = false"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="deleteModal" x-transition:enter="ease-out duration-300"
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
                                <h3 class="text-lg font-bold text-gray-900">Hapus Pengguna</h3>
                                <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-gray-600">Anda yakin ingin menghapus pengguna <span
                                    class="font-semibold text-gray-900" x-text="selectedUserName"></span>? Semua data
                                terkait pengguna ini akan dihapus.</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" @click="deleteModal = false"
                            class="px-4 py-2.5 text-gray-700 font-medium rounded-xl hover:bg-gray-100 transition-colors">Batal</button>
                        <form :action="'/admin/users/' + selectedUserId" method="POST" class="inline">
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

        <!-- Bulk Action Form (Hidden) -->
        <form id="bulkActionForm" action="{{ route('admin.users.bulk-action') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="action" x-bind:value="bulkActionType">
            <template x-for="userId in selectedUsers" :key="userId">
                <input type="hidden" name="user_ids[]" :value="userId">
            </template>
        </form>
    </div>

    <script>
        function userManagement() {
            return {
                selectedUsers: [],
                resetPasswordModal: false,
                changeRoleModal: false,
                deleteModal: false,
                selectedUserId: null,
                selectedUserName: '',
                selectedRole: 'siswa',
                bulkActionType: '',

                toggleUser(userId) {
                    if (this.selectedUsers.includes(userId)) {
                        this.selectedUsers = this.selectedUsers.filter(id => id !== userId);
                    } else {
                        this.selectedUsers.push(userId);
                    }
                },

                toggleSelectAll(event) {
                    if (event.target.checked) {
                        this.selectedUsers = [
                            @foreach($users as $user)
                                {{ $user->id }},
                            @endforeach
                    ];
                    } else {
                        this.selectedUsers = [];
                    }
                },

                clearSelection() {
                    this.selectedUsers = [];
                },

                showResetPasswordModal(userId, userName) {
                    this.selectedUserId = userId;
                    this.selectedUserName = userName;
                    this.resetPasswordModal = true;
                },

                showChangeRoleModal(userId, userName, currentRole) {
                    this.selectedUserId = userId;
                    this.selectedUserName = userName;
                    this.selectedRole = currentRole;
                    this.changeRoleModal = true;
                },

                showDeleteModal(userId, userName) {
                    this.selectedUserId = userId;
                    this.selectedUserName = userName;
                    this.deleteModal = true;
                },

                bulkAction(action) {
                    if (this.selectedUsers.length === 0) return;

                    let confirmMsg = '';
                    switch (action) {
                        case 'activate':
                            confirmMsg = `Aktifkan ${this.selectedUsers.length} pengguna?`;
                            break;
                        case 'deactivate':
                            confirmMsg = `Nonaktifkan ${this.selectedUsers.length} pengguna?`;
                            break;
                        case 'delete':
                            confirmMsg = `Hapus ${this.selectedUsers.length} pengguna? Tindakan ini tidak dapat dibatalkan.`;
                            break;
                        case 'reset_password':
                            confirmMsg = `Reset password ${this.selectedUsers.length} pengguna?`;
                            break;
                    }

                    if (confirm(confirmMsg)) {
                        this.bulkActionType = action;
                        this.$nextTick(() => {
                            document.getElementById('bulkActionForm').submit();
                        });
                    }
                }
            }
        }
    </script>
@endsection