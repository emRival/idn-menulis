@extends('layouts.app')

@section('title', 'Tulis Artikel - IDN Menulis')

@section('styles')
<style>
    /* Editor Styles */
    .editor-container {
        min-height: calc(100vh - 140px);
    }

    .tox-tinymce {
        border: none !important;
        border-radius: 0 !important;
    }

    .tox-editor-header {
        box-shadow: none !important;
        border-bottom: 1px solid #e5e7eb !important;
    }

    .tox-toolbar__primary {
        background: #fafafa !important;
    }

    .tox-edit-area__iframe {
        background: #fff !important;
    }

    /* Focus Mode */
    .focus-mode .sidebar-panel {
        display: none;
    }

    .focus-mode .editor-main {
        max-width: 800px;
        margin: 0 auto;
    }

    .focus-mode .editor-header-extras {
        opacity: 0;
        transition: opacity 0.3s;
    }

    .focus-mode:hover .editor-header-extras {
        opacity: 1;
    }

    /* Autosave indicator animation */
    @keyframes pulse-save {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .saving-indicator {
        animation: pulse-save 1s infinite;
    }

    /* Title input */
    .title-input {
        font-size: 2.5rem;
        line-height: 1.2;
        border: none;
        outline: none;
        width: 100%;
        background: transparent;
    }

    .title-input::placeholder {
        color: #d1d5db;
    }

    .title-input:focus {
        outline: none;
    }

    /* Image upload */
    .cover-upload {
        aspect-ratio: 16/9;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    }

    .cover-upload.has-image {
        background: transparent;
    }

    /* Schedule picker */
    .schedule-picker {
        display: none;
    }

    .schedule-picker.active {
        display: block;
    }

    /* Tag pills */
    .tag-pill {
        transition: all 0.2s;
    }

    .tag-pill:hover {
        transform: translateY(-1px);
    }

    /* Status bar */
    .status-bar {
        backdrop-filter: blur(8px);
        background: rgba(255, 255, 255, 0.9);
    }

    /* Preview modal */
    .preview-modal {
        backdrop-filter: blur(4px);
    }
</style>
@endsection

@section('content')
<div x-data="articleEditor()"
     :class="{ 'focus-mode': focusMode }"
     @keydown.ctrl.s.prevent="saveDraft()"
     @keydown.meta.s.prevent="saveDraft()">

    <!-- Sticky Header -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Logo & Status -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-primary-600 font-bold">
                        <svg class="w-8 h-8" viewBox="0 0 40 40" fill="none">
                            <rect width="40" height="40" rx="10" fill="currentColor"/>
                            <path d="M12 28V12h4l4 10 4-10h4v16h-3V17l-3.5 9h-3L15 17v11h-3z" fill="white"/>
                        </svg>
                        <span class="hidden sm:inline">IDN Menulis</span>
                    </a>

                    <div class="h-6 w-px bg-gray-200"></div>

                    <!-- Status Badge -->
                    <div class="flex items-center gap-2">
                        <span :class="{
                            'bg-gray-100 text-gray-600': status === 'draft',
                            'bg-yellow-100 text-yellow-700': status === 'pending',
                            'bg-green-100 text-green-700': status === 'published'
                        }" class="px-2.5 py-1 rounded-full text-xs font-medium">
                            <span x-text="status === 'draft' ? 'Draft' : status === 'pending' ? 'Menunggu Review' : 'Dipublikasi'"></span>
                        </span>

                        <!-- Auto-save indicator -->
                        <span x-show="isSaving" class="saving-indicator flex items-center gap-1 text-xs text-gray-500">
                            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                        <span x-show="!isSaving && lastSaved" class="text-xs text-gray-400">
                            <span class="hidden sm:inline">Tersimpan</span> <span x-text="lastSaved"></span>
                        </span>
                    </div>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-2 editor-header-extras">
                    <!-- Focus Mode Toggle -->
                    <button type="button" @click="focusMode = !focusMode"
                            :class="focusMode ? 'bg-primary-100 text-primary-600' : 'text-gray-500 hover:bg-gray-100'"
                            class="hidden lg:flex items-center gap-2 px-3 py-2 rounded-lg transition-colors"
                            title="Mode Fokus">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm" x-text="focusMode ? 'Keluar Fokus' : 'Mode Fokus'"></span>
                    </button>

                    <!-- Preview Button -->
                    <button type="button" @click="showPreview = true"
                            class="flex items-center gap-2 px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span class="hidden sm:inline text-sm">Preview</span>
                    </button>

                    <!-- Save Draft -->
                    <button type="button" @click="saveDraft()"
                            :disabled="isSaving"
                            class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors disabled:opacity-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <span class="hidden sm:inline text-sm">Simpan</span>
                    </button>

                    <!-- Publish/Submit -->
                    <button type="button" @click="showPublishModal = true"
                            class="flex items-center gap-2 px-4 py-2 bg-primary-500 text-white rounded-lg font-medium hover:bg-primary-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span class="text-sm">Publish</span>
                    </button>

                    <!-- Cancel -->
                    <a href="{{ route('dashboard') }}"
                       class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                       title="Batal">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Editor Area -->
    <form id="articleForm" action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="action" x-model="submitAction">
        <input type="hidden" name="scheduled_at" x-model="scheduledAt">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
            <!-- Error Display -->
            @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                <div class="flex items-center gap-2 text-red-700 font-medium mb-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Terdapat beberapa kesalahan:
                </div>
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Main Content Area -->
                <div class="flex-1 editor-main">
                    <!-- Cover Image -->
                    <div class="mb-6">
                        <div class="cover-upload relative rounded-2xl overflow-hidden border-2 border-dashed border-gray-200 hover:border-primary-400 transition-colors cursor-pointer"
                             :class="{ 'has-image border-solid border-transparent': coverPreview }"
                             @click="$refs.coverInput.click()"
                             @dragover.prevent="isDragging = true"
                             @dragleave.prevent="isDragging = false"
                             @drop.prevent="handleCoverDrop($event)">

                            <!-- Placeholder -->
                            <div x-show="!coverPreview" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-16 h-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="font-medium text-gray-600">Tambahkan Cover Image</p>
                                <p class="text-sm mt-1">Klik atau drag & drop • JPG, PNG, WEBP • Maks 2MB</p>
                            </div>

                            <!-- Preview -->
                            <img x-show="coverPreview" :src="coverPreview" alt="Cover Preview" class="w-full h-full object-cover">

                            <!-- Remove Button -->
                            <button x-show="coverPreview" type="button" @click.stop="removeCover()"
                                    class="absolute top-4 right-4 w-10 h-10 bg-black/50 hover:bg-black/70 text-white rounded-xl flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                            <input type="file" name="featured_image" x-ref="coverInput" class="hidden"
                                   accept="image/jpeg,image/png,image/webp" @change="handleCoverChange($event)">
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="mb-6">
                        <input type="text" name="title" x-model="title"
                               class="title-input font-display font-bold text-gray-900 placeholder-gray-300"
                               placeholder="Tulis Judul Artikel di Sini..."
                               @input="updateStats()" required>
                    </div>

                    <!-- Excerpt (Subtitle) -->
                    <div class="mb-8">
                        <textarea name="excerpt" x-model="excerpt"
                                  placeholder="Tambahkan ringkasan singkat (opsional)..."
                                  class="w-full text-xl text-gray-500 placeholder-gray-300 border-none outline-none resize-none bg-transparent"
                                  rows="2" @input="autoResize($event.target); updateStats()"></textarea>
                    </div>

                    <!-- Editor -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <textarea name="content" id="content" x-ref="editor">{{ old('content') }}</textarea>
                    </div>
                </div>

                <!-- Sidebar Panel -->
                <aside class="sidebar-panel w-full lg:w-80 space-y-6">
                    <!-- Category -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3">
                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Kategori
                        </label>
                        <select name="category_id" x-model="category"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->icon ?? '📁' }} {{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tags -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3">
                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                            Tag <span class="font-normal text-gray-400">(maks. 5)</span>
                        </label>
                        <div class="flex flex-wrap gap-2 max-h-40 overflow-y-auto custom-scrollbar">
                            @foreach($tags as $tag)
                            <label class="tag-pill cursor-pointer">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="peer hidden"
                                       @change="limitTags($event, 5)">
                                <span class="inline-flex items-center px-3 py-1.5 text-sm rounded-lg border-2 border-gray-200 text-gray-600
                                             peer-checked:border-primary-500 peer-checked:bg-primary-50 peer-checked:text-primary-700
                                             hover:border-gray-300 transition-all">
                                    #{{ $tag->name }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Level -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3">
                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Level Pembaca
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="level" value="pemula" class="peer hidden" checked>
                                <span class="block text-center px-3 py-2 text-sm rounded-lg border-2 border-gray-200
                                             peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700
                                             hover:border-gray-300 transition-all">
                                    Pemula
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="level" value="menengah" class="peer hidden">
                                <span class="block text-center px-3 py-2 text-sm rounded-lg border-2 border-gray-200
                                             peer-checked:border-yellow-500 peer-checked:bg-yellow-50 peer-checked:text-yellow-700
                                             hover:border-gray-300 transition-all">
                                    Menengah
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="level" value="lanjut" class="peer hidden">
                                <span class="block text-center px-3 py-2 text-sm rounded-lg border-2 border-gray-200
                                             peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700
                                             hover:border-gray-300 transition-all">
                                    Lanjut
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Quick Tips -->
                    <div class="bg-gradient-to-br from-primary-50 to-blue-50 rounded-2xl p-5 border border-primary-100">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="text-xl">💡</span> Tips Menulis
                        </h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-primary-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Gunakan judul yang menarik perhatian
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-primary-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Tambahkan gambar untuk visual yang menarik
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-primary-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Struktur dengan heading dan paragraf
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-primary-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Review sebelum publish
                            </li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </form>

    <!-- Footer Status Bar -->
    <footer class="status-bar fixed bottom-0 left-0 right-0 border-t border-gray-100 py-3 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between text-sm text-gray-500">
                <div class="flex items-center gap-6">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span x-text="wordCount"></span> kata
                    </span>
                    <span class="hidden sm:flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-text="readingTime"></span> menit baca
                    </span>
                    <span class="hidden md:flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        <span x-text="charCount"></span> karakter
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-gray-400">Ctrl+S untuk simpan</span>
                    <span x-show="lastSaved" class="text-gray-400">
                        Terakhir disimpan: <span x-text="lastSaved"></span>
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Publish Modal -->
    <div x-show="showPublishModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="preview-modal fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
         @click.self="showPublishModal = false">

        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="p-6 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900">Publikasikan Artikel</h3>
                <p class="text-gray-500 text-sm mt-1">Pilih kapan artikel ini akan dipublikasikan</p>
            </div>

            <div class="p-6 space-y-4">
                <!-- Publish Now -->
                <label class="block p-4 border-2 rounded-xl cursor-pointer transition-all"
                       :class="publishOption === 'now' ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                    <div class="flex items-center gap-3">
                        <input type="radio" x-model="publishOption" value="now" class="hidden">
                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                             :class="publishOption === 'now' ? 'border-primary-500' : 'border-gray-300'">
                            <div x-show="publishOption === 'now'" class="w-3 h-3 rounded-full bg-primary-500"></div>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Publikasikan Sekarang</p>
                            <p class="text-sm text-gray-500">Artikel akan dikirim untuk review</p>
                        </div>
                    </div>
                </label>

                <!-- Schedule -->
                <label class="block p-4 border-2 rounded-xl cursor-pointer transition-all"
                       :class="publishOption === 'schedule' ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                    <div class="flex items-center gap-3">
                        <input type="radio" x-model="publishOption" value="schedule" class="hidden">
                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                             :class="publishOption === 'schedule' ? 'border-primary-500' : 'border-gray-300'">
                            <div x-show="publishOption === 'schedule'" class="w-3 h-3 rounded-full bg-primary-500"></div>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Jadwalkan Publikasi</p>
                            <p class="text-sm text-gray-500">Pilih tanggal dan waktu (WIB)</p>
                        </div>
                    </div>
                </label>

                <!-- Schedule Picker -->
                <div x-show="publishOption === 'schedule'" x-transition class="pl-8 space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                            <input type="date" x-model="scheduleDate"
                                   :min="minDate"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu (WIB)</label>
                            <input type="time" x-model="scheduleTime"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Waktu Indonesia Barat (WIB / UTC+7)
                    </p>
                </div>
            </div>

            <div class="p-6 bg-gray-50 flex gap-3">
                <button type="button" @click="showPublishModal = false"
                        class="flex-1 px-4 py-3 border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-100 transition-colors">
                    Batal
                </button>
                <button type="button" @click="submitArticle()"
                        class="flex-1 px-4 py-3 bg-primary-500 text-white rounded-xl font-medium hover:bg-primary-600 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <span x-text="publishOption === 'schedule' ? 'Jadwalkan' : 'Publish'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div x-show="showPreview"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 bg-black/50 z-50 overflow-y-auto"
         @click.self="showPreview = false">

        <div class="min-h-screen py-8 px-4">
            <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="sticky top-0 bg-white border-b border-gray-100 p-4 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900">Preview Artikel</h3>
                    <button @click="showPreview = false" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-8">
                    <!-- Cover -->
                    <div x-show="coverPreview" class="aspect-video rounded-xl overflow-hidden mb-8">
                        <img :src="coverPreview" alt="Cover" class="w-full h-full object-cover">
                    </div>

                    <!-- Title -->
                    <h1 class="text-4xl font-bold font-display text-gray-900 mb-4" x-text="title || 'Judul Artikel'"></h1>

                    <!-- Excerpt -->
                    <p class="text-xl text-gray-500 mb-8" x-text="excerpt || 'Ringkasan artikel...'"></p>

                    <!-- Content -->
                    <div class="prose max-w-none" x-html="previewContent"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- TinyMCE (Self-hosted CDN - no API key required) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
function articleEditor() {
    return {
        // State
        title: '{{ old("title") }}',
        excerpt: '{{ old("excerpt") }}',
        category: '{{ old("category_id") }}',
        status: 'draft',
        coverPreview: null,
        isDragging: false,

        // Modals
        showPublishModal: false,
        showPreview: false,
        focusMode: false,

        // Publishing
        publishOption: 'now',
        scheduleDate: '',
        scheduleTime: '09:00',
        scheduledAt: '',
        submitAction: 'draft',

        // Stats
        wordCount: 0,
        charCount: 0,
        readingTime: 0,

        // Auto-save
        isSaving: false,
        lastSaved: null,
        autoSaveTimer: null,

        // Computed
        get minDate() {
            const now = new Date();
            return now.toISOString().split('T')[0];
        },

        get previewContent() {
            if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                return tinymce.get('content').getContent();
            }
            return '';
        },

        // Methods
        init() {
            this.initTinyMCE();
            this.startAutoSave();

            // Set default schedule date to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            this.scheduleDate = tomorrow.toISOString().split('T')[0];
        },

        initTinyMCE() {
            tinymce.init({
                selector: '#content',
                license_key: 'gpl',
                promotion: false,
                height: 500,
                menubar: true,
                // Prevent URL conversion to relative paths
                relative_urls: false,
                remove_script_host: false,
                convert_urls: false,
                document_base_url: '{{ url('/') }}/',
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount', 'codesample',
                    'emoticons', 'directionality', 'visualchars', 'pagebreak', 'nonbreaking',
                    'quickbars', 'autoresize'
                ],
                toolbar: [
                    'undo redo | cut copy paste pastetext | bold italic underline strikethrough | subscript superscript | removeformat',
                    'blocks fontfamily fontsize | forecolor backcolor | alignleft aligncenter alignright alignjustify | lineheight',
                    'bullist numlist | outdent indent | blockquote codesample code | hr pagebreak nonbreaking',
                    'link unlink anchor | image media emoticons charmap | table tabledelete | tableprops tablerowprops tablecellprops',
                    'tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
                    'visualblocks visualchars | searchreplace | ltr rtl | fullscreen preview print | help'
                ],
                toolbar_mode: 'wrap',
                block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4',
                content_style: `
                    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap');
                    body {
                        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                        font-size: 18px;
                        line-height: 1.8;
                        color: #374151;
                        padding: 2rem;
                        max-width: 100%;
                    }
                    h1, h2, h3, h4, h5, h6 {
                        font-family: 'Poppins', sans-serif;
                        font-weight: 600;
                        color: #111827;
                        margin-top: 2rem;
                        margin-bottom: 1rem;
                    }
                    h2 { font-size: 1.75rem; }
                    h3 { font-size: 1.5rem; }
                    h4 { font-size: 1.25rem; }
                    p { margin-bottom: 1.5rem; }
                    a { color: #0d9488; text-decoration: underline; }
                    blockquote {
                        border-left: 4px solid #14b8a6;
                        padding: 1rem 1.5rem;
                        margin: 1.5rem 0;
                        font-style: italic;
                        color: #6b7280;
                        background: #f0fdfa;
                        border-radius: 0 1rem 1rem 0;
                    }
                    pre {
                        background: #1f2937;
                        color: #e5e7eb;
                        padding: 1.5rem;
                        border-radius: 0.75rem;
                        overflow-x: auto;
                    }
                    code {
                        background: #f3f4f6;
                        padding: 0.2rem 0.5rem;
                        border-radius: 0.25rem;
                        font-size: 0.9em;
                    }
                    pre code {
                        background: transparent;
                        padding: 0;
                    }
                    img {
                        max-width: 100%;
                        height: auto;
                        border-radius: 1rem;
                        margin: 1.5rem auto;
                    }
                    ul, ol { margin: 1.5rem 0; padding-left: 1.5rem; }
                    li { margin-bottom: 0.5rem; }
                    table { border-collapse: collapse; width: 100%; margin: 1.5rem 0; }
                    th, td { border: 1px solid #e5e7eb; padding: 0.75rem; text-align: left; }
                    th { background: #f9fafb; font-weight: 600; }
                `,
                placeholder: 'Mulai menulis artikelmu di sini...',
                branding: false,
                promotion: false,
                statusbar: false,
                resize: true,
                setup: (editor) => {
                    editor.on('input', () => this.updateStats());
                    editor.on('change', () => this.updateStats());
                },
                images_upload_handler: (blobInfo, progress) => {
                    return new Promise((resolve, reject) => {
                        const formData = new FormData();
                        formData.append('image', blobInfo.blob(), blobInfo.filename());
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                        fetch('{{ route("articles.upload-image") }}', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.url) {
                                resolve(result.url);
                            } else {
                                reject('Upload gagal');
                            }
                        })
                        .catch(() => reject('Upload gagal'));
                    });
                }
            });
        },

        updateStats() {
            const titleWords = this.title.trim().split(/\s+/).filter(w => w.length > 0).length;
            const excerptWords = this.excerpt.trim().split(/\s+/).filter(w => w.length > 0).length;

            let contentWords = 0;
            let contentChars = 0;

            if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                const text = tinymce.get('content').getContent({ format: 'text' });
                contentWords = text.trim().split(/\s+/).filter(w => w.length > 0).length;
                contentChars = text.length;
            }

            this.wordCount = titleWords + excerptWords + contentWords;
            this.charCount = this.title.length + this.excerpt.length + contentChars;
            this.readingTime = Math.max(1, Math.ceil(this.wordCount / 200));
        },

        startAutoSave() {
            this.autoSaveTimer = setInterval(() => {
                if (this.title.length > 0 || this.getEditorContent().length > 0) {
                    this.saveDraft(true);
                }
            }, 30000); // Auto-save every 30 seconds
        },

        getEditorContent() {
            if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                return tinymce.get('content').getContent();
            }
            return '';
        },

        async saveDraft(isAuto = false) {
            if (this.isSaving) return;

            this.isSaving = true;
            this.submitAction = 'draft';

            // Save to localStorage for recovery
            const draftData = {
                title: this.title,
                excerpt: this.excerpt,
                content: this.getEditorContent(),
                category: this.category,
                savedAt: new Date().toISOString()
            };
            localStorage.setItem('article_draft', JSON.stringify(draftData));

            // Update last saved time
            const now = new Date();
            this.lastSaved = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

            setTimeout(() => {
                this.isSaving = false;
            }, 1000);

            if (!isAuto) {
                // Sync TinyMCE content before submit
                if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                    tinymce.get('content').save();
                }
                // Ensure action is set to draft
                this.$nextTick(() => {
                    document.querySelector('input[name="action"]').value = 'draft';
                    document.getElementById('articleForm').submit();
                });
            }
        },

        submitArticle() {
            this.submitAction = 'publish';

            if (this.publishOption === 'schedule') {
                // Convert to ISO format with Jakarta timezone
                const dateTime = `${this.scheduleDate}T${this.scheduleTime}:00`;
                this.scheduledAt = dateTime;
            } else {
                this.scheduledAt = '';
            }

            this.showPublishModal = false;

            // Sync TinyMCE content before submit
            if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                tinymce.get('content').save();
            }

            // Use $nextTick to ensure Alpine has updated the hidden inputs
            this.$nextTick(() => {
                // Double-check: directly set hidden input values to ensure they're submitted
                document.querySelector('input[name="action"]').value = 'publish';
                document.querySelector('input[name="scheduled_at"]').value = this.scheduledAt;
                document.getElementById('articleForm').submit();
            });
        },

        handleCoverChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.previewCover(file);
            }
        },

        handleCoverDrop(event) {
            this.isDragging = false;
            const file = event.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                this.$refs.coverInput.files = event.dataTransfer.files;
                this.previewCover(file);
            }
        },

        previewCover(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.coverPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        removeCover() {
            this.coverPreview = null;
            this.$refs.coverInput.value = '';
        },

        limitTags(event, max) {
            const checked = document.querySelectorAll('input[name="tags[]"]:checked');
            if (checked.length > max) {
                event.target.checked = false;
            }
        },

        autoResize(el) {
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
        },

        // Cleanup
        destroy() {
            if (this.autoSaveTimer) {
                clearInterval(this.autoSaveTimer);
            }
            if (typeof tinymce !== 'undefined') {
                tinymce.remove('#content');
            }
        }
    }
}
</script>
@endsection
