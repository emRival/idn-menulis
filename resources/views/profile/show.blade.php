@extends('layouts.app')

@section('title', $user->full_name . ' - Profil IDN Menulis')
@section('meta_title', $user->full_name . ' - Profil IDN Menulis')
@section('meta_description', 'Profil penulis ' . $user->full_name . ' di IDN Menulis.')
@section('og_type', 'profile')
@section('styles')
<style>
    .cover-section {
        background: linear-gradient(135deg, #0d9488 0%, #115e59 100%);
        height: 280px;
        position: relative;
        overflow: hidden;
    }
    .cover-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: absolute;
        inset: 0;
    }
    .cover-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.3) 100%);
    }
    .avatar-wrapper {
        position: relative;
        margin-top: -5rem; /* -80px to pull avatar up */
        z-index: 20;
    }
    .avatar-ring {
        padding: 0.25rem;
        background-color: white;
        border-radius: 9999px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        display: inline-block;
        position: relative;
    }
    .avatar-img {
        width: 160px;
        height: 160px;
        border-radius: 9999px;
        object-fit: cover;
    }
    .avatar-upload-btn {
        position: absolute;
        bottom: 0.5rem;
        right: 0.5rem;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 9999px;
        background-color: #0d9488;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        border: 3px solid white;
        z-index: 30;
    }
    .avatar-upload-btn:hover {
        background-color: #0f766e;
        transform: scale(1.1);
    }
    .cover-change-btn {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 20;
    }
    .stat-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .tab-btn {
        transition: all 0.2s;
        position: relative;
    }
    .tab-btn.active {
        color: #0d9488;
        border-bottom: 2px solid #0d9488;
    }
    .tab-btn:not(.active):hover {
        color: #6b7280;
        background: #f9fafb;
    }
    .progress-ring {
        transform: rotate(-90deg);
    }
    .article-card {
        transition: all 0.2s ease;
    }
    .article-card:hover {
        background-color: #f9fafb;
    }
    .badge-level {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    [x-cloak] { display: none !important; }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 pb-12" x-data="profileManager()">
    <!-- Cover Section -->
    <div class="cover-section">
        @if($user->cover_image)
            <img src="{{ Storage::url($user->cover_image) }}" alt="Cover" class="cover-image">
        @endif
        <div class="cover-overlay"></div>

        <!-- Cover Change Button -->
        @if(auth()->id() === $user->id)
            <label class="cover-change-btn flex items-center gap-2 px-4 py-2 bg-white/90 backdrop-blur-sm rounded-lg shadow-lg cursor-pointer hover:bg-white transition text-gray-700 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-sm font-medium">Ganti Cover</span>
                <input type="file" class="hidden" accept="image/*" @change="uploadCover($event)">
            </label>
        @endif
    </div>

    <!-- Profile Info -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl relative z-10 -mt-12 overflow-visible">
            <div class="p-6 md:p-8 pt-0">
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Avatar Section -->
                    <div class="avatar-wrapper flex-shrink-0 mx-auto md:mx-0">
                        <div class="avatar-ring">
                            <img src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name) . '&size=160&background=0d9488&color=fff' }}"
                                 alt="{{ $user->full_name }}"
                                 class="avatar-img shadow-inner"
                                 id="avatar-preview">
                            
                            @if(auth()->id() === $user->id)
                                <label class="avatar-upload-btn shadow-md" title="Ganti Foto Profil">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <input type="file" class="hidden" accept="image/*" @change="uploadAvatar($event)">
                                </label>
                            @endif
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="flex-1 text-center md:text-left">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                            <div>
                                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $user->full_name }}</h1>
                                <p class="text-gray-500 text-lg">{{ '@' . $user->username }}</p>

                                <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mt-3">
                                    <!-- Role Badge -->
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium
                                        @if($user->role === 'admin') bg-red-100 text-red-700
                                        @elseif($user->role === 'guru') bg-blue-100 text-blue-700
                                        @else bg-green-100 text-green-700 @endif">
                                        @if($user->role === 'admin')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif($user->role === 'guru')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        @endif
                                        {{ ucfirst($user->role) }}
                                    </span>

                                    @if($user->school_name)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            {{ $user->school_name }}
                                        </span>
                                    @endif

                                    @if($user->class)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                            Kelas {{ $user->class }}
                                        </span>
                                    @endif

                                    <!-- Level Badge -->
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm badge-level text-white font-medium">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        @php
                                            $level = 'Pemula';
                                            if ($stats['total_articles'] >= 50) $level = 'Master';
                                            elseif ($stats['total_articles'] >= 20) $level = 'Ahli';
                                            elseif ($stats['total_articles'] >= 10) $level = 'Mahir';
                                            elseif ($stats['total_articles'] >= 5) $level = 'Terampil';
                                        @endphp
                                        {{ $level }}
                                    </span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mt-4 md:mt-0">
                                @if(auth()->user()->isAdmin() && auth()->id() === $user->id)
                                    <a href="{{ route('dashboard') }}"
                                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-900 text-white rounded-xl hover:bg-gray-800 transition shadow-lg shadow-gray-900/20 text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                        </svg>
                                        Dashboard
                                    </a>
                                @endif

                                <div class="flex items-center gap-3">
                                    <a href="{{ route('profile.edit') }}"
                                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition shadow-lg shadow-primary-600/20 text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <a href="{{ route('profile.password') }}"
                                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition text-sm font-medium hover:border-gray-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Keamanan
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Bio -->
                        @if($user->bio)
                            <p class="mt-4 text-gray-600 leading-relaxed max-w-2xl">{{ $user->bio }}</p>
                        @else
                            <p class="mt-4 text-gray-400 italic">Belum ada bio. <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:underline">Tambahkan sekarang</a></p>
                        @endif

                        <!-- Profile Completion -->
                        @if($profileCompletion['percentage'] < 100)
                            <div class="mt-6 p-4 bg-gradient-to-r from-primary-50 to-teal-50 rounded-xl border border-primary-100">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <svg class="progress-ring w-16 h-16" viewBox="0 0 36 36">
                                            <path class="text-gray-200" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                            <path class="text-primary-500" stroke="currentColor" stroke-width="3" stroke-linecap="round" fill="none" stroke-dasharray="{{ $profileCompletion['percentage'] }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                        </svg>
                                        <span class="absolute inset-0 flex items-center justify-center text-sm font-bold text-primary-600">{{ $profileCompletion['percentage'] }}%</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">Lengkapi Profilmu!</p>
                                        <p class="text-sm text-gray-600">Profil yang lengkap membantu orang lain mengenalmu lebih baik.</p>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach($profileCompletion['fields'] as $field => $completed)
                                                @if(!$completed)
                                                    <span class="text-xs px-2 py-1 bg-white rounded-full text-gray-600">
                                                        @switch($field)
                                                            @case('avatar') Foto Profil @break
                                                            @case('bio') Bio @break
                                                            @case('full_name') Nama Lengkap @break
                                                            @case('school_name') Nama Sekolah @break
                                                            @case('class') Kelas @break
                                                        @endswitch
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 px-6 md:px-8 pb-6 md:pb-8">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Bergabung</p>
                            <p class="text-sm font-medium text-gray-900">{{ $user->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Terakhir Login</p>
                            <p class="text-sm font-medium text-gray-900">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Baru saja' }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Status</p>
                            <p class="text-sm font-medium {{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-6">
            <div class="stat-card bg-white p-5 rounded-xl shadow-sm border">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_articles'] }}</p>
                        <p class="text-sm text-gray-500">Total Artikel</p>
                    </div>
                </div>
            </div>
            <div class="stat-card bg-white p-5 rounded-xl shadow-sm border">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_views']) }}</p>
                        <p class="text-sm text-gray-500">Total Views</p>
                    </div>
                </div>
            </div>
            <div class="stat-card bg-white p-5 rounded-xl shadow-sm border">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_likes']) }}</p>
                        <p class="text-sm text-gray-500">Total Likes</p>
                    </div>
                </div>
            </div>
            <div class="stat-card bg-white p-5 rounded-xl shadow-sm border">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_comments']) }}</p>
                        <p class="text-sm text-gray-500">Total Komentar</p>
                    </div>
                </div>
            </div>
            <div class="stat-card bg-white p-5 rounded-xl shadow-sm border">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_bookmarks']) }}</p>
                        <p class="text-sm text-gray-500">Bookmark</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-xl shadow-sm border mt-6 overflow-hidden">
            <div class="border-b">
                <nav class="flex overflow-x-auto">
                    <button @click="activeTab = 'articles'"
                            :class="activeTab === 'articles' ? 'active' : ''"
                            class="tab-btn flex items-center gap-2 px-6 py-4 font-medium text-gray-500 whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Artikel Saya
                        <span class="text-xs px-2 py-0.5 rounded-full bg-primary-100 text-primary-600">{{ $stats['total_articles'] }}</span>
                    </button>
                    <button @click="activeTab = 'favorites'"
                            :class="activeTab === 'favorites' ? 'active' : ''"
                            class="tab-btn flex items-center gap-2 px-6 py-4 font-medium text-gray-500 whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                        </svg>
                        Favorit
                        <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-600">{{ $stats['total_bookmarks'] }}</span>
                    </button>
                    <button @click="activeTab = 'comments'"
                            :class="activeTab === 'comments' ? 'active' : ''"
                            class="tab-btn flex items-center gap-2 px-6 py-4 font-medium text-gray-500 whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Komentar
                        <span class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-600">{{ $stats['comments_made'] }}</span>
                    </button>
                    <button @click="activeTab = 'achievements'"
                            :class="activeTab === 'achievements' ? 'active' : ''"
                            class="tab-btn flex items-center gap-2 px-6 py-4 font-medium text-gray-500 whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                        Prestasi
                    </button>
                    <button @click="activeTab = 'settings'"
                            :class="activeTab === 'settings' ? 'active' : ''"
                            class="tab-btn flex items-center gap-2 px-6 py-4 font-medium text-gray-500 whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Pengaturan
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Articles Tab -->
                <div x-show="activeTab === 'articles'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <!-- Status Filter -->
                    <div class="flex flex-wrap gap-2 mb-6">
                        <button @click="articleFilter = 'all'"
                                :class="articleFilter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition">
                            Semua ({{ $stats['total_articles'] }})
                        </button>
                        <button @click="articleFilter = 'published'"
                                :class="articleFilter === 'published' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition">
                            Dipublikasikan ({{ $stats['published_articles'] }})
                        </button>
                        <button @click="articleFilter = 'pending'"
                                :class="articleFilter === 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition">
                            Menunggu ({{ $stats['pending_articles'] }})
                        </button>
                        <button @click="articleFilter = 'draft'"
                                :class="articleFilter === 'draft' ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition">
                            Draft ({{ $stats['draft_articles'] }})
                        </button>
                    </div>

                    <!-- Articles List -->
                    @if($articles->count() > 0)
                        <div class="space-y-4">
                            @foreach($articles as $article)
                                <div class="article-card flex gap-4 p-4 border rounded-xl">
                                    <div class="flex-shrink-0">
                                        @if($article->featured_image)
                                            <img src="{{ Storage::url($article->featured_image) }}"
                                                 alt="{{ $article->title }}"
                                                 class="w-24 h-16 object-cover rounded-lg">
                                        @else
                                            <div class="w-24 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="min-w-0">
                                                <h4 class="font-semibold text-gray-900 truncate">{{ $article->title }}</h4>
                                                <p class="text-sm text-gray-500">
                                                    {{ $article->category->name ?? 'Tanpa Kategori' }} • {{ $article->created_at->format('d M Y') }}
                                                </p>
                                            </div>
                                            <span class="flex-shrink-0 px-2 py-1 rounded-full text-xs font-medium
                                                @if($article->status === 'published') bg-green-100 text-green-700
                                                @elseif($article->status === 'pending') bg-yellow-100 text-yellow-700
                                                @elseif($article->status === 'revision') bg-orange-100 text-orange-700
                                                @elseif($article->status === 'rejected') bg-red-100 text-red-700
                                                @else bg-gray-100 text-gray-700 @endif">
                                                {{ ucfirst($article->status) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                {{ number_format($article->views_count) }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ $article->likedBy()->count() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($article->status === 'published')
                                            <a href="{{ route('articles.show', $article) }}"
                                               class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Lihat">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                        @endif
                                        @if(in_array($article->status, ['draft', 'rejected', 'revision']))
                                            <a href="{{ route('articles.edit', $article) }}"
                                               class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($stats['total_articles'] > 5)
                            <div class="mt-6 text-center">
                                <a href="{{ route('articles.my') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                    Lihat Semua Artikel →
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-gray-500 mb-4">Belum ada artikel yang ditulis</p>
                            <a href="{{ route('articles.create') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tulis Artikel Pertama
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Favorites Tab -->
                <div x-show="activeTab === 'favorites'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak>
                    @if($bookmarks->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($bookmarks as $bookmark)
                                <div class="article-card flex gap-4 p-4 border rounded-xl">
                                    <div class="flex-shrink-0">
                                        @if($bookmark->featured_image)
                                            <img src="{{ Storage::url($bookmark->featured_image) }}"
                                                 alt="{{ $bookmark->title }}"
                                                 class="w-20 h-16 object-cover rounded-lg">
                                        @else
                                            <div class="w-20 h-16 bg-gradient-to-br from-amber-100 to-amber-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 truncate">
                                            <a href="{{ route('articles.show', $bookmark) }}" class="hover:text-blue-600">{{ $bookmark->title }}</a>
                                        </h4>
                                        <p class="text-sm text-gray-500">
                                            {{ $bookmark->user->full_name ?? $bookmark->user->username }} • {{ $bookmark->category->name ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($stats['total_bookmarks'] > 5)
                            <div class="mt-6 text-center">
                                <a href="{{ route('bookmarks.index') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                    Lihat Semua Bookmark →
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                            <p class="text-gray-500">Belum ada artikel yang disimpan</p>
                        </div>
                    @endif
                </div>

                <!-- Comments Tab -->
                <div x-show="activeTab === 'comments'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak>
                    @if($comments->count() > 0)
                        <div class="space-y-4">
                            @foreach($comments as $comment)
                                <div class="p-4 border rounded-xl">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-gray-700 line-clamp-2">{{ $comment->content }}</p>
                                            <p class="text-sm text-gray-500 mt-2">
                                                Pada artikel:
                                                <a href="{{ route('articles.show', $comment->article) }}" class="text-blue-600 hover:underline">
                                                    {{ $comment->article->title }}
                                                </a>
                                            </p>
                                        </div>
                                        <span class="text-xs text-gray-400 flex-shrink-0">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p class="text-gray-500">Belum ada komentar yang dibuat</p>
                        </div>
                    @endif
                </div>

                <!-- Achievements Tab -->
                <div x-show="activeTab === 'achievements'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gradient-to-br from-amber-50 to-orange-50 p-6 rounded-xl border border-amber-200">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">Level Penulis</h3>
                                    <p class="text-2xl font-bold text-amber-600">{{ $level }}</p>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600">
                                @if($stats['total_articles'] < 5)
                                    Tulis {{ 5 - $stats['total_articles'] }} artikel lagi untuk naik level!
                                @elseif($stats['total_articles'] < 10)
                                    Tulis {{ 10 - $stats['total_articles'] }} artikel lagi untuk jadi Mahir!
                                @elseif($stats['total_articles'] < 20)
                                    Tulis {{ 20 - $stats['total_articles'] }} artikel lagi untuk jadi Ahli!
                                @elseif($stats['total_articles'] < 50)
                                    Tulis {{ 50 - $stats['total_articles'] }} artikel lagi untuk jadi Master!
                                @else
                                    Selamat! Kamu sudah mencapai level tertinggi!
                                @endif
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-xl border border-green-200">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">Total Views</h3>
                                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['total_views']) }}</p>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600">
                                @if($stats['total_views'] >= 10000)
                                    Influencer! Artikelmu dibaca banyak orang!
                                @elseif($stats['total_views'] >= 1000)
                                    Trending! Terus menulis artikel menarik!
                                @else
                                    Terus promosikan artikelmu untuk lebih banyak views!
                                @endif
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-pink-50 to-rose-50 p-6 rounded-xl border border-pink-200">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-pink-400 to-rose-500 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">Engagement</h3>
                                    <p class="text-2xl font-bold text-pink-600">{{ $stats['total_likes'] + $stats['total_comments'] }}</p>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $stats['total_likes'] }} likes dan {{ $stats['total_comments'] }} komentar dari pembaca
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="font-semibold text-gray-900 mb-4">Badge Koleksi</h3>
                        <div class="flex flex-wrap gap-4">
                            @if($stats['total_articles'] >= 1)
                                <div class="flex items-center gap-2 px-4 py-2 bg-blue-100 text-blue-700 rounded-full">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                    Penulis Pertama
                                </div>
                            @endif
                            @if($stats['total_views'] >= 100)
                                <div class="flex items-center gap-2 px-4 py-2 bg-green-100 text-green-700 rounded-full">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    100+ Views
                                </div>
                            @endif
                            @if($stats['total_likes'] >= 10)
                                <div class="flex items-center gap-2 px-4 py-2 bg-red-100 text-red-700 rounded-full">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                    </svg>
                                    Disukai Banyak
                                </div>
                            @endif
                            @if($stats['published_articles'] >= 5)
                                <div class="flex items-center gap-2 px-4 py-2 bg-purple-100 text-purple-700 rounded-full">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                    Konsisten
                                </div>
                            @endif
                            @if($stats['comments_made'] >= 10)
                                <div class="flex items-center gap-2 px-4 py-2 bg-amber-100 text-amber-700 rounded-full">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    Aktif Berkomentar
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div x-show="activeTab === 'settings'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak>
                    <div class="max-w-2xl space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-4 p-4 border rounded-xl hover:bg-gray-50 transition">
                                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Edit Profil</h4>
                                    <p class="text-sm text-gray-500">Ubah foto, bio, dan informasi lainnya</p>
                                </div>
                            </a>
                            <a href="{{ route('profile.password') }}"
                               class="flex items-center gap-4 p-4 border rounded-xl hover:bg-gray-50 transition">
                                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Ubah Password</h4>
                                    <p class="text-sm text-gray-500">Perbarui password akun</p>
                                </div>
                            </a>
                        </div>

                        <div class="p-6 border border-red-200 rounded-xl bg-red-50">
                            <h4 class="font-semibold text-red-700 mb-2">Zona Berbahaya</h4>
                            <p class="text-sm text-red-600 mb-4">Tindakan berikut bersifat permanen dan tidak dapat dibatalkan.</p>
                            <form action="{{ route('profile.delete') }}" method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun? Semua data Anda akan dihapus secara permanen.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus Akun
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function profileManager() {
    return {
        activeTab: 'articles',
        articleFilter: 'all',
        uploading: false,

        // Client-side image compression using Canvas
        compressImage(file, maxWidth, maxHeight, quality = 0.8) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        let { width, height } = img;

                        // Scale down if larger than max dimensions
                        if (width > maxWidth || height > maxHeight) {
                            const ratio = Math.min(maxWidth / width, maxHeight / height);
                            width = Math.round(width * ratio);
                            height = Math.round(height * ratio);
                        }

                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            if (blob) {
                                resolve(new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), {
                                    type: 'image/jpeg',
                                    lastModified: Date.now(),
                                }));
                            } else {
                                reject(new Error('Gagal mengkompresi gambar'));
                            }
                        }, 'image/jpeg', quality);
                    };
                    img.onerror = () => reject(new Error('Gagal memuat gambar'));
                    img.src = e.target.result;
                };
                reader.onerror = () => reject(new Error('Gagal membaca file'));
                reader.readAsDataURL(file);
            });
        },

        async uploadCover(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file size (max 10MB input)
            if (file.size > 10 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 10MB.');
                return;
            }

            this.uploading = true;

            try {
                // Compress: max 1920px wide, JPEG 80% quality
                const compressed = await this.compressImage(file, 1920, 1080, 0.8);
                console.log(`Cover compressed: ${(file.size/1024).toFixed(0)}KB → ${(compressed.size/1024).toFixed(0)}KB`);

                const formData = new FormData();
                formData.append('cover_image', compressed);
                formData.append('_token', '{{ csrf_token() }}');

                const response = await fetch('{{ route("profile.cover.update") }}', {
                    method: 'POST',
                    body: formData,
                });

                const data = await response.json();
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal mengupload cover: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error uploading cover:', error);
                alert('Terjadi kesalahan: ' + error.message);
            } finally {
                this.uploading = false;
            }
        },

        async uploadAvatar(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file size (max 5MB input)
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 5MB.');
                return;
            }

            this.uploading = true;

            try {
                // Compress: max 512px, JPEG 80% quality
                const compressed = await this.compressImage(file, 512, 512, 0.8);
                console.log(`Avatar compressed: ${(file.size/1024).toFixed(0)}KB → ${(compressed.size/1024).toFixed(0)}KB`);

                const formData = new FormData();
                formData.append('avatar', compressed);
                formData.append('_token', '{{ csrf_token() }}');

                const response = await fetch('{{ route("profile.avatar.update") }}', {
                    method: 'POST',
                    body: formData,
                });

                const data = await response.json();
                if (data.success) {
                    document.getElementById('avatar-preview').src = data.avatar_url;
                } else {
                    alert('Gagal mengupload avatar: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error uploading avatar:', error);
                alert('Terjadi kesalahan: ' + error.message);
            } finally {
                this.uploading = false;
            }
        }
    }
}
</script>
@endsection
