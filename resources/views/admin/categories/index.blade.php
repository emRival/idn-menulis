@extends('layouts.app')

@section('title', 'Manajemen Kategori - Admin IDN Menulis')

@section('content')
    <div x-data="categoryManagement()" class="min-h-screen bg-gray-50">
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
                            <span class="text-gray-900 font-medium">Categories</span>
                        </nav>
                        <h1 class="text-xl font-bold text-gray-900">Manajemen Kategori</h1>
                    </div>
                    <button @click="openCreateModal()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Tambah Kategori
                    </button>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
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

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-red-800">{{ session('error') }}</p>
                    <button @click="$el.parentElement.remove()" class="ml-auto text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                            <p class="text-sm text-gray-500">Total Kategori</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                            <p class="text-sm text-gray-500">Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['hidden'] }}</p>
                            <p class="text-sm text-gray-500">Hidden</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div>
                            @if($stats['popular'])
                                <p class="text-lg font-bold text-gray-900 truncate max-w-[120px]">{{ $stats['popular']->name }}
                                </p>
                                <p class="text-sm text-gray-500">Terpopuler</p>
                            @else
                                <p class="text-lg font-bold text-gray-400">-</p>
                                <p class="text-sm text-gray-500">Terpopuler</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Filter Bar -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('admin.categories.index') }}"
                        class="flex flex-col sm:flex-row gap-3">
                        <!-- Search -->
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama kategori..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>

                        <!-- Status Filter -->
                        <select name="status"
                            class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white min-w-[140px]">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Hidden</option>
                        </select>

                        <!-- Sort -->
                        <select name="sort"
                            class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white min-w-[180px]">
                            <option value="order" {{ request('sort', 'order') === 'order' ? 'selected' : '' }}>Urutan Posisi
                            </option>
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>A – Z</option>
                            <option value="articles" {{ request('sort') === 'articles' ? 'selected' : '' }}>Terbanyak
                                Digunakan</option>
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                        </select>

                        <button type="submit"
                            class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">
                            Filter
                        </button>

                        @if(request()->hasAny(['search', 'status', 'sort']))
                            <a href="{{ route('admin.categories.index') }}"
                                class="px-5 py-2.5 text-gray-500 font-medium rounded-xl hover:bg-gray-100 transition-colors">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Bulk Action Bar -->
                <div x-show="selectedIds.length > 0" x-cloak
                    class="px-4 py-3 bg-blue-50 border-t border-blue-100 flex items-center gap-4">
                    <span class="text-sm text-blue-700 font-medium">
                        <span x-text="selectedIds.length"></span> kategori dipilih
                    </span>
                    <div class="flex items-center gap-2 ml-auto">
                        <button @click="bulkAction('activate')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Aktifkan
                        </button>
                        <button @click="bulkAction('deactivate')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                            Nonaktifkan
                        </button>
                        <button @click="bulkAction('delete')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus
                        </button>
                        <button @click="selectedIds = []"
                            class="px-3 py-1.5 text-blue-600 text-sm font-medium hover:text-blue-800 transition-colors">
                            Batal
                        </button>
                    </div>
                </div>
            </div>

            <!-- Categories Table -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="w-12 px-4 py-3">
                                    <input type="checkbox" @change="toggleSelectAll($event)"
                                        :checked="selectedIds.length === {{ $categories->count() }} && selectedIds.length > 0"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Kategori</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Slug</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">
                                    Deskripsi</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Artikel</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">
                                    Dibuat</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($categories as $category)
                                <tr class="hover:bg-gray-50/50 transition-colors"
                                    :class="{ 'bg-blue-50/50': selectedIds.includes({{ $category->id }}) }">
                                    <td class="px-4 py-4">
                                        <input type="checkbox" :value="{{ $category->id }}" x-model.number="selectedIds"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg"
                                                style="background-color: {{ $category->color }}20; color: {{ $category->color }}">
                                                {{ $category->icon ?? '📂' }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $category->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <code
                                            class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-md font-mono">{{ $category->slug }}</code>
                                    </td>
                                    <td class="px-4 py-4 hidden lg:table-cell">
                                        <p class="text-sm text-gray-500 line-clamp-1 max-w-xs">
                                            {{ $category->description ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span
                                            class="inline-flex items-center justify-center min-w-[40px] px-2.5 py-1 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">
                                            {{ $category->articles_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <button @click="toggleStatus({{ $category->id }})"
                                            class="relative inline-flex items-center h-6 w-11 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 {{ $category->is_active ? 'bg-green-500' : 'bg-gray-300' }}">
                                            <span
                                                class="inline-block w-4 h-4 transform rounded-full bg-white shadow-md transition-transform {{ $category->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                        </button>
                                    </td>
                                    <td class="px-4 py-4 hidden md:table-cell">
                                        <span class="text-sm text-gray-500">{{ $category->created_at->format('d M Y') }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button @click="openEditModal({{ $category->id }})"
                                                class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button
                                                @click="confirmDelete({{ $category->id }}, '{{ $category->name }}', {{ $category->articles_count }})"
                                                class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
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
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Belum ada kategori</p>
                                        <p class="text-sm text-gray-400 mt-1">Klik tombol "Tambah Kategori" untuk membuat
                                            kategori baru.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($categories->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div x-show="showFormModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showFormModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="closeFormModal()"
                    class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="showFormModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submitForm()">
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center gap-4 mb-6">
                                <div
                                    class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900"
                                        x-text="isEditing ? 'Edit Kategori' : 'Tambah Kategori'"></h3>
                                    <p class="text-sm text-gray-500"
                                        x-text="isEditing ? 'Perbarui informasi kategori' : 'Buat kategori baru untuk artikel'">
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" x-model="form.name" @input="generateSlug()" required
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                        placeholder="Contoh: Tutorial">
                                    <p x-show="errors.name" x-text="errors.name" class="mt-1 text-sm text-red-600"></p>
                                </div>

                                <!-- Slug (Auto) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                                    <div class="relative">
                                        <input type="text" x-model="form.slug" readonly
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500 font-mono text-sm">
                                        <span
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Auto-generate</span>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                                    <textarea x-model="form.description" rows="3"
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 resize-none"
                                        placeholder="Deskripsi singkat kategori..."></textarea>
                                </div>

                                <!-- Icon & Color Row -->
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Icon/Emoji -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Icon / Emoji</label>
                                        <input type="text" x-model="form.icon" maxlength="10"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-center text-xl"
                                            placeholder="📂">
                                    </div>

                                    <!-- Color -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Label</label>
                                        <div class="flex items-center gap-3">
                                            <input type="color" x-model="form.color"
                                                class="w-12 h-12 border border-gray-200 rounded-xl cursor-pointer">
                                            <input type="text" x-model="form.color" maxlength="7"
                                                class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-mono text-sm uppercase"
                                                placeholder="#3B82F6">
                                        </div>
                                    </div>
                                </div>

                                <!-- Status (only for editing) -->
                                <div x-show="isEditing">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <label
                                        class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50">
                                        <input type="checkbox" x-model="form.is_active"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <div>
                                            <p class="font-medium text-gray-900">Kategori Aktif</p>
                                            <p class="text-xs text-gray-500">Kategori akan ditampilkan di website</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                            <button type="button" @click="closeFormModal()"
                                class="px-4 py-2.5 text-gray-700 font-medium rounded-xl hover:bg-gray-100 transition-colors">Batal</button>
                            <button type="submit" :disabled="isSubmitting"
                                class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!isSubmitting"
                                    x-text="isEditing ? 'Simpan Perubahan' : 'Tambah Kategori'"></span>
                                <span x-show="isSubmitting" class="flex items-center gap-2">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
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
                                <h3 class="text-lg font-bold text-gray-900">Hapus Kategori</h3>
                                <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-gray-600 text-sm">
                                Anda yakin ingin menghapus kategori <span class="font-semibold text-gray-900"
                                    x-text="deleteTarget.name"></span>?
                            </p>
                            <div x-show="deleteTarget.articlesCount > 0"
                                class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-xl">
                                <p class="text-yellow-800 text-sm flex items-center gap-2">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Kategori ini memiliki <span class="font-semibold"
                                        x-text="deleteTarget.articlesCount"></span> artikel dan tidak dapat dihapus.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" @click="showDeleteModal = false"
                            class="px-4 py-2.5 text-gray-700 font-medium rounded-xl hover:bg-gray-100 transition-colors">Batal</button>
                        <button @click="deleteCategory()" :disabled="deleteTarget.articlesCount > 0"
                            class="px-4 py-2.5 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">Ya,
                            Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function categoryManagement() {
            return {
                selectedIds: [],
                showFormModal: false,
                showDeleteModal: false,
                isEditing: false,
                editingId: null,
                isSubmitting: false,
                form: {
                    name: '',
                    slug: '',
                    description: '',
                    icon: '📂',
                    color: '#3B82F6',
                    is_active: true,
                },
                errors: {},
                deleteTarget: {
                    id: null,
                    name: '',
                    articlesCount: 0,
                },

                generateSlug() {
                    this.form.slug = this.form.name
                        .toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .trim();
                },

                toggleSelectAll(event) {
                    if (event.target.checked) {
                        this.selectedIds = @json($categories->pluck('id'));
                    } else {
                        this.selectedIds = [];
                    }
                },

                openCreateModal() {
                    this.isEditing = false;
                    this.editingId = null;
                    this.form = {
                        name: '',
                        slug: '',
                        description: '',
                        icon: '📂',
                        color: '#3B82F6',
                        is_active: true,
                    };
                    this.errors = {};
                    this.showFormModal = true;
                },

                async openEditModal(id) {
                    this.isEditing = true;
                    this.editingId = id;
                    this.errors = {};

                    try {
                        const response = await fetch(`{{ url('admin/categories') }}/${id}/data`);
                        const data = await response.json();
                        if (data.success) {
                            this.form = {
                                name: data.category.name,
                                slug: data.category.slug,
                                description: data.category.description || '',
                                icon: data.category.icon || '📂',
                                color: data.category.color || '#3B82F6',
                                is_active: data.category.is_active,
                            };
                            this.showFormModal = true;
                        }
                    } catch (error) {
                        console.error('Error fetching category:', error);
                    }
                },

                closeFormModal() {
                    this.showFormModal = false;
                    this.isEditing = false;
                    this.editingId = null;
                },

                async submitForm() {
                    this.isSubmitting = true;
                    this.errors = {};

                    const url = this.isEditing
                        ? `{{ url('admin/categories') }}/${this.editingId}`
                        : '{{ route('admin.categories.store') }}';

                    const method = this.isEditing ? 'PUT' : 'POST';

                    console.log('Submitting to:', url, 'Method:', method, 'Data:', this.form);

                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(this.form),
                        });

                        console.log('Response status:', response.status);

                        const text = await response.text();
                        console.log('Response text:', text.substring(0, 500));

                        let data;
                        try {
                            data = JSON.parse(text);
                        } catch (e) {
                            console.error('Failed to parse JSON:', e);
                            alert('Server error: ' + text.substring(0, 200));
                            return;
                        }

                        if (response.ok && data.success) {
                            window.location.reload();
                        } else if (response.status === 422) {
                            // Validation errors
                            if (data.errors) {
                                for (const key in data.errors) {
                                    this.errors[key] = data.errors[key][0];
                                }
                            }
                        } else {
                            alert(data.message || 'Terjadi kesalahan');
                        }
                    } catch (error) {
                        console.error('Error submitting form:', error);
                        alert('Network error: ' + error.message);
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                async toggleStatus(id) {
                    try {
                        const response = await fetch(`{{ url('admin/categories') }}/${id}/toggle-status`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        });

                        const data = await response.json();
                        if (data.success) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Error toggling status:', error);
                    }
                },

                confirmDelete(id, name, articlesCount) {
                    this.deleteTarget = { id, name, articlesCount };
                    this.showDeleteModal = true;
                },

                async deleteCategory() {
                    console.log('Delete called, target:', this.deleteTarget);

                    if (this.deleteTarget.articlesCount > 0) {
                        alert('Kategori dengan artikel tidak dapat dihapus');
                        return;
                    }

                    try {
                        const response = await fetch(`{{ url('admin/categories') }}/${this.deleteTarget.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                        });

                        console.log('Delete response status:', response.status);

                        const text = await response.text();
                        console.log('Delete response:', text.substring(0, 500));

                        let data;
                        try {
                            data = JSON.parse(text);
                        } catch (e) {
                            console.error('Failed to parse JSON:', e);
                            alert('Server error: ' + text.substring(0, 200));
                            return;
                        }

                        if (response.ok && data.success) {
                            this.showDeleteModal = false;
                            window.location.reload();
                        } else {
                            alert(data.message || 'Gagal menghapus kategori');
                        }
                    } catch (error) {
                        console.error('Error deleting category:', error);
                        alert('Network error: ' + error.message);
                    }
                },

                async bulkAction(action) {
                    if (this.selectedIds.length === 0) return;

                    const confirmMessages = {
                        activate: 'Aktifkan kategori yang dipilih?',
                        deactivate: 'Nonaktifkan kategori yang dipilih?',
                        delete: 'Hapus kategori yang dipilih? Kategori dengan artikel tidak akan dihapus.',
                    };

                    if (!confirm(confirmMessages[action])) return;

                    try {
                        const response = await fetch('{{ route('admin.categories.bulk-action') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                action: action,
                                ids: this.selectedIds,
                            }),
                        });

                        const data = await response.json();
                        if (data.success) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Error performing bulk action:', error);
                    }
                },
            }
        }
    </script>
@endsection