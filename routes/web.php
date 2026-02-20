<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\Auth\TwoFactorController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kategori', [HomeController::class, 'categories'])->name('categories.index');
Route::get('/kategori/{category:slug}', [HomeController::class, 'category'])->name('categories.show');
Route::get('/tag', [HomeController::class, 'tags'])->name('tags.index');
Route::get('/tag/{tag:slug}', [HomeController::class, 'tag'])->name('tags.show');
Route::get('/cari', [HomeController::class, 'search'])->name('articles.search');
Route::get('/penulis', [ProfileController::class, 'writers'])->name('writers.index');

// Article listing (public)
Route::get('/artikel', [ArticleController::class, 'index'])->name('articles.index');

// Auth routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function (\Illuminate\Http\Request $request) {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        if (config('services.turnstile.secret')) {
            $rules['cf-turnstile-response'] = [
                'required',
                function ($attribute, $value, $fail) {
                    try {
                        $response = \Illuminate\Support\Facades\Http::timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                            'secret' => config('services.turnstile.secret'),
                            'response' => $value,
                            'remoteip' => request()->ip(),
                        ]);

                        if (!$response->successful() || !$response->json('success')) {
                            $fail('Validasi Captcha gagal. Silakan coba lagi.');
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Turnstile Validation Error: ' . $e->getMessage());
                        // Fail gracefully or strictly? strictly for security.
                        $fail('Gagal menghubungi server validasi Captcha. Cek koneksi internet server.');
                    }
                }
            ];
        }

        $credentials = $request->validate($rules, [
            'cf-turnstile-response.required' => 'Silakan selesaikan Captcha keamanan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Remove Turnstile response from credentials to prevent SQL error
        unset($credentials['cf-turnstile-response']);

        // Rate limiting check with progressive delay
        $throttleKey = 'login_attempt_' . $request->ip();
        $maxAttempts = config('security.brute_force.max_attempts', 5);

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $message = "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.";

            // Log brute force attempt
            \Illuminate\Support\Facades\Log::warning('Brute force attempt detected', [
                'ip' => $request->ip(),
                'email' => $credentials['email'],
                'user_agent' => $request->userAgent(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 429);
            }

            return back()->withErrors(['email' => $message]);
        }

        // Check if user exists and is locked
        $user = \App\Models\User::where('email', $credentials['email'])->first();
        if ($user && $user->locked_until && $user->locked_until > now()) {
            $minutes = now()->diffInMinutes($user->locked_until);
            $message = "Akun Anda dikunci. Silakan coba lagi dalam {$minutes} menit.";

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 423);
            }

            return back()->withErrors(['email' => $message]);
        }

        if (\Illuminate\Support\Facades\Auth::attempt($credentials, $request->boolean('remember'))) {
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);

            // Regenerate session for security
            $request->session()->regenerate();

            // Rotate CSRF token
            $request->session()->regenerateToken();

            $user = \Illuminate\Support\Facades\Auth::user();

            // Reset failed login count
            $user->update([
                'failed_login_count' => 0,
                'locked_until' => null,
            ]);

            // Record login activity
            $user->recordLogin($request->ip());

            // Log successful login
            \App\Models\LoginAttempt::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'successful' => true,
                'attempted_at' => now(),
            ]);

            // Log activity
            if (config('security.logging.log_login', true)) {
                \App\Models\ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'login',
                    'description' => 'User logged in successfully',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'redirect' => route('dashboard')
                ]);
            }

            return redirect()->intended('/dashboard');
        }

        // Handle failed login
        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, config('security.brute_force.lockout_time', 60) * 60);

        // Update failed login count for user
        if ($user) {
            $failedCount = ($user->failed_login_count ?? 0) + 1;
            $updateData = ['failed_login_count' => $failedCount];

            // Lock account after too many failures
            if ($failedCount >= config('security.brute_force.block_ip_after', 10)) {
                $updateData['locked_until'] = now()->addMinutes(config('security.brute_force.lockout_time', 60));

                \Illuminate\Support\Facades\Log::warning('Account locked due to too many failed attempts', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'failed_count' => $failedCount,
                ]);
            }

            $user->update($updateData);
        }

        // Log failed attempt
        \App\Models\LoginAttempt::create([
            'user_id' => $user?->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'successful' => false,
            'attempted_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
                'errors' => [
                    'email' => ['Email atau password salah.']
                ]
            ], 422);
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    });

    Route::middleware(['check.registration', 'throttle:6,1'])->group(function () {
        Route::get('/register', function () {
            return view('auth.register');
        })->name('register');

        Route::post('/register', function (\Illuminate\Http\Request $request) {
            $rules = [
                'username' => 'required|alpha_dash|min:4|max:50|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8|confirmed',
                'full_name' => 'required|string|min:3',
            ];

            if (config('services.turnstile.secret')) {
                $rules['cf-turnstile-response'] = [
                    'required',
                    function ($attribute, $value, $fail) {
                        try {
                            $response = \Illuminate\Support\Facades\Http::timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                                'secret' => config('services.turnstile.secret'),
                                'response' => $value,
                                'remoteip' => request()->ip(),
                            ]);

                            if (!$response->successful() || !$response->json('success')) {
                                $fail('Validasi Captcha gagal. Silakan coba lagi.');
                            }
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Turnstile Register Validation Error: ' . $e->getMessage());
                            $fail('Gagal menghubungi server validasi Captcha. Cek koneksi internet server.');
                        }
                    }
                ];
            }

            $validated = $request->validate($rules);

            $user = \App\Models\User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'full_name' => $validated['full_name'],
                'role' => 'siswa',
            ]);

            \Illuminate\Support\Facades\Auth::login($user);

            return redirect('/dashboard');
        });
    });

    // Password Reset Routes
    Route::get('/lupa-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/lupa-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'update'])->name('password.update');
});

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/articles', [ArticleController::class, 'myArticles'])->name('dashboard.articles');
    Route::post('/dashboard/articles/bulk-delete', [ArticleController::class, 'bulkDelete'])->name('articles.bulk-delete');
    Route::post('/dashboard/articles/bulk-submit', [ArticleController::class, 'bulkSubmit'])->name('articles.bulk-submit');
    Route::post('/artikel/{article}/duplicate', [ArticleController::class, 'duplicate'])->name('articles.duplicate');

    // Article Management
    Route::get('/artikel/buat', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/artikel', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/artikel/{article}/sunting', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/artikel/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/artikel/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    Route::post('/artikel/{article}/publikasikan', [ArticleController::class, 'publish'])->name('articles.publish');
    Route::post('/artikel/{article}/jadwal', [ArticleController::class, 'schedule'])->name('articles.schedule');
    Route::post('/artikel/{article}/kembali-ke-draft', [ArticleController::class, 'revertToDraft'])->name('articles.revert-draft');
    Route::post('/artikel/upload-gambar', [ArticleController::class, 'uploadImage'])->name('articles.upload-image');

    // Comments
    Route::post('/artikel/{article}/komentar', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/komentar/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/komentar/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/komentar/{comment}/like', [CommentController::class, 'toggleLike'])->name('comments.like');
    Route::post('/komentar/{comment}/reaksi', [CommentController::class, 'react'])->name('comments.react');
    Route::get('/komentar/{comment}/balasan', [CommentController::class, 'getReplies'])->name('comments.replies');

    // Likes
    Route::post('/artikel/{article}/like', [LikeController::class, 'toggle'])->name('likes.toggle');

    // Bookmarks
    Route::post('/artikel/{article}/bookmark', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::get('/bookmark-saya', [BookmarkController::class, 'myBookmarks'])->name('bookmarks.index');

    // Profile
    Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profil/sunting', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profil/ubah-password', [ProfileController::class, 'editPassword'])->name('profile.password');
    Route::put('/profil/ubah-password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profil', [ProfileController::class, 'delete'])->name('profile.delete');
    Route::get('/profil/artikel', [ProfileController::class, 'getArticles'])->name('profile.articles');
    Route::get('/profil/bookmark', [ProfileController::class, 'getBookmarks'])->name('profile.bookmarks');
    Route::get('/profil/komentar', [ProfileController::class, 'getComments'])->name('profile.comments');
    Route::post('/profil/cover', [ProfileController::class, 'updateCover'])->name('profile.cover.update');
    Route::post('/profil/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');

    // Notifications
    Route::post('/notifikasi/baca-semua', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    })->name('notifications.markAllRead');

    Route::get('/notifikasi', function () {
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('notifications.index', compact('notifications'));
    })->name('notifications.index');

    Route::post('/notifikasi/{notification}/baca', function ($notificationId) {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
        return back();
    })->name('notifications.read');
});

// Guru & Admin routes
Route::middleware(['auth', 'role:guru,admin'])->group(function () {
    // Approvals - use explicit ID binding instead of slug
    Route::get('/persetujuan', [ApprovalController::class, 'pending'])->name('approvals.pending');
    Route::get('/persetujuan/{article}', [ApprovalController::class, 'show'])->name('approvals.show')->whereNumber('article');
    Route::get('/persetujuan/{article}/data', [ApprovalController::class, 'getData'])->name('approvals.data')->whereNumber('article');
    Route::post('/persetujuan/{article}/setujui', [ApprovalController::class, 'approve'])->name('approvals.approve')->whereNumber('article');
    Route::post('/persetujuan/{article}/revisi', [ApprovalController::class, 'revision'])->name('approvals.revision')->whereNumber('article');
    Route::post('/persetujuan/{article}/tolak', [ApprovalController::class, 'reject'])->name('approvals.reject')->whereNumber('article');
    Route::post('/persetujuan/bulk-action', [ApprovalController::class, 'bulkAction'])->name('approvals.bulk-action');
    Route::get('/persetujuan/{article}/riwayat', [ApprovalController::class, 'history'])->name('approvals.history')->whereNumber('article');

    // Comment moderation
    Route::post('/komentar/{comment}/setujui', [CommentController::class, 'approve'])->name('comments.approve');
    Route::post('/komentar/{comment}/tolak', [CommentController::class, 'reject'])->name('comments.reject');
    Route::post('/komentar/{comment}/hapus-admin', [CommentController::class, 'adminDelete'])->name('comments.admin-delete');

    // Toggle article comments
    Route::post('/artikel/{article}/toggle-komentar', [ArticleController::class, 'toggleComments'])->name('articles.toggle-comments');
});

// Admin routes only
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // SEO Analyzer
    Route::get('/seo', [\App\Http\Controllers\Admin\SEOAnalyzerController::class, 'index'])->name('seo.index');
    Route::get('/seo/analyze/{article}', [\App\Http\Controllers\Admin\SEOAnalyzerController::class, 'analyze'])->name('seo.analyze');
    Route::post('/seo/bulk-analyze', [\App\Http\Controllers\Admin\SEOAnalyzerController::class, 'bulkAnalyze'])->name('seo.bulk-analyze');
    Route::get('/seo/suggestions/{article}', [\App\Http\Controllers\Admin\SEOAnalyzerController::class, 'suggestions'])->name('seo.suggestions');

    // Users
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/aktifkan', [UserController::class, 'activate'])->name('users.activate');
    Route::post('/users/{user}/nonaktifkan', [UserController::class, 'deactivate'])->name('users.deactivate');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/users/{user}/ubah-role', [UserController::class, 'changeRole'])->name('users.change-role');
    Route::post('/users/{user}/notifikasi', [UserController::class, 'sendNotification'])->name('users.notify');
    Route::post('/users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk-action');
    Route::get('/users-export', [UserController::class, 'export'])->name('users.export');
    Route::get('/users/{user}/data', [UserController::class, 'getData'])->name('users.data');

    // Categories
    Route::resource('categories', CategoryController::class);
    Route::post('/categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::post('/categories/bulk-action', [CategoryController::class, 'bulkAction'])->name('categories.bulk-action');
    Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
    Route::get('/categories/{category}/data', [CategoryController::class, 'getData'])->name('categories.data');

    // Tags
    Route::resource('tags', TagController::class);

    // Settings
    Route::get('/pengaturan', function () {
        $registrationEnabled = \App\Models\Setting::registrationEnabled();
        return view('admin.settings.index', compact('registrationEnabled'));
    })->name('settings');

    // Toggle Registration
    Route::post('/pengaturan/toggle-registration', function (\Illuminate\Http\Request $request) {
        $enabled = $request->boolean('enabled');
        \App\Models\Setting::set('registration_enabled', $enabled ? '1' : '0');

        return response()->json([
            'success' => true,
            'enabled' => $enabled,
            'message' => $enabled ? 'Pendaftaran berhasil diaktifkan.' : 'Pendaftaran berhasil dinonaktifkan.',
        ]);
    })->name('settings.toggle-registration');

    // Activity logs
    Route::get('/aktivitas', function () {
        $logs = \App\Models\ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        return view('admin.activity-logs', compact('logs'));
    })->name('activity-logs');

    // Security logs
    Route::get('/security-logs', function () {
        $query = \App\Models\SecurityLog::with('user')
            ->orderBy('created_at', 'desc');

        if (request('severity')) {
            $query->where('severity', request('severity'));
        }
        if (request('event_type')) {
            $query->where('event_type', request('event_type'));
        }
        if (request('ip')) {
            $query->where('ip_address', request('ip'));
        }

        $logs = $query->paginate(50);

        $stats = [
            'total_24h' => \App\Models\SecurityLog::where('created_at', '>=', now()->subHours(24))->count(),
            'failed_logins' => \App\Models\LoginAttempt::where('successful', false)->where('attempted_at', '>=', now()->subHours(24))->count(),
            'critical' => \App\Models\SecurityLog::getCriticalEventsCount(),
            'blocked_ips' => count(\App\Models\SecurityLog::getSuspiciousIPs()),
        ];

        return view('admin.security-logs', compact('logs', 'stats'));
    })->name('security-logs');
});

// Article show route (must be after /artikel/buat to avoid conflict)
Route::get('/artikel/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');

// Trending API endpoint
Route::get('/api/trending-articles', [ArticleController::class, 'trending']);
Route::get('/api/likes/{article}', [LikeController::class, 'count']);

// Publish scheduled articles API (for JavaScript polling)
Route::post('/api/publish-scheduled', function () {
    $count = \Illuminate\Support\Facades\DB::table('articles')
        ->where('status', 'scheduled')
        ->whereNotNull('scheduled_at')
        ->where('scheduled_at', '<=', now())
        ->update([
            'status' => 'published',
            'published_at' => now(),
            'scheduled_at' => null,
            'updated_at' => now(),
        ]);

    return response()->json([
        'success' => true,
        'published' => $count,
        'timestamp' => now()->toIso8601String()
    ]);
});

// SEO Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-articles.xml', [SitemapController::class, 'articles'])->name('sitemap.articles');
Route::get('/sitemap-categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap-images.xml', [SitemapController::class, 'images'])->name('sitemap.images');
Route::get('/sitemap-news.xml', [SitemapController::class, 'news'])->name('sitemap.news');
Route::get('/sitemap-authors.xml', [SitemapController::class, 'authors'])->name('sitemap.authors');

// RSS Feeds
Route::get('/feed', [FeedController::class, 'rss'])->name('feed.rss');
Route::get('/feed.xml', [FeedController::class, 'rss']);
Route::get('/rss', [FeedController::class, 'rss']);
Route::get('/feed/atom', [FeedController::class, 'atom'])->name('feed.atom');
Route::get('/feed.json', [FeedController::class, 'json'])->name('feed.json');

Route::get('/robots.txt', function () {
    return response()->file(public_path('robots.txt'));
});

// Two-Factor Authentication Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/two-factor', [TwoFactorController::class, 'index'])->name('two-factor.index');
    Route::get('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::delete('/two-factor', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
    Route::post('/two-factor/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.recovery-codes');
});

// 2FA Challenge (for login with 2FA)
Route::get('/two-factor-challenge', [TwoFactorController::class, 'challenge'])->name('two-factor.challenge');
Route::post('/two-factor-challenge', [TwoFactorController::class, 'verify'])->name('two-factor.verify');

// Security Profile Route
Route::middleware(['auth'])->group(function () {
    Route::get('/profil/keamanan', function () {
        return view('profile.security', [
            'user' => auth()->user(),
        ]);
    })->name('profile.security');

    // Logout all devices
    Route::post('/profil/logout-semua', function (\Illuminate\Http\Request $request) {
        $request->validate(['password' => 'required|current_password']);

        app(\App\Services\SecurityService::class)->invalidateAllSessions(auth()->user());

        return back()->with('success', 'Anda telah logout dari semua perangkat lain.');
    })->name('profile.logout-all');
});
