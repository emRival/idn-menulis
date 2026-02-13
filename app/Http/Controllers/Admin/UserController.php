<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display list of users.
     */
    public function index(Request $request): View
    {
        $query = User::withCount(['articles', 'comments']);

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Filter by active status
        if ($request->filled('status')) {
            if ($request->input('status') === 'pending') {
                $query->whereNull('email_verified_at');
            } else {
                $query->where('is_active', $request->input('status'));
            }
        }

        // Filter by class
        if ($request->filled('class')) {
            $query->where('class', $request->input('class'));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $users = $query->paginate(20)->withQueryString();

        // Get unique classes for filter
        $classes = User::whereNotNull('class')->distinct()->pluck('class')->sort();

        // Stats
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'pending' => User::whereNull('email_verified_at')->count(),
        ];

        return view('admin.users.index', compact('users', 'classes', 'stats'));
    }

    /**
     * Show create user form.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store new user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'full_name' => 'required|string|max:255',
            'role' => 'required|in:admin,guru,siswa',
            'class' => 'nullable|string|max:50',
            'school_name' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;
        $validated['email_verified_at'] = now();

        $user = User::create($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_user',
            'description' => "Membuat pengguna baru: {$user->full_name}",
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Show user detail.
     */
    public function show(User $user): View
    {
        $user->load([
            'articles' => function ($q) {
                $q->latest()->limit(10);
            },
            'comments' => function ($q) {
                $q->with('article')->latest()->limit(10);
            },
            'approvals',
            'activityLogs' => function ($q) {
                $q->latest()->limit(20);
            }
        ]);

        // Get article stats
        $articleStats = [
            'total' => $user->articles()->count(),
            'published' => $user->articles()->where('status', 'published')->count(),
            'draft' => $user->articles()->where('status', 'draft')->count(),
            'pending' => $user->articles()->where('status', 'pending_review')->count(),
            'total_views' => $user->articles()->sum('views_count'),
        ];

        return view('admin.users.show', compact('user', 'articleStats'));
    }

    /**
     * Show edit form.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_user',
            'description' => "Mengupdate data pengguna: {$user->full_name}",
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Activate user.
     */
    public function activate(User $user): RedirectResponse
    {
        $user->update(['is_active' => true]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'activate_user',
            'description' => "Mengaktifkan pengguna: {$user->full_name}",
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'Pengguna diaktifkan.');
    }

    /**
     * Deactivate user.
     */
    public function deactivate(User $user): RedirectResponse
    {
        $user->update(['is_active' => false]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deactivate_user',
            'description' => "Menonaktifkan pengguna: {$user->full_name}",
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'Pengguna dinonaktifkan.');
    }

    /**
     * Delete user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $userName = $user->full_name;
        $user->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_user',
            'description' => "Menghapus pengguna: {$userName}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $newPassword = $request->input('new_password') ?? Str::random(10);

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'reset_password',
            'description' => "Mereset password pengguna: {$user->full_name}",
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Password berhasil direset. Password baru: {$newPassword}");
    }

    /**
     * Change user role.
     */
    public function changeRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,guru,siswa',
        ]);

        $oldRole = $user->role;
        $user->update(['role' => $validated['role']]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'change_role',
            'description' => "Mengubah role pengguna {$user->full_name} dari {$oldRole} ke {$validated['role']}",
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Role pengguna berhasil diubah.');
    }

    /**
     * Bulk action on users.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete,reset_password',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $users = User::whereIn('id', $validated['user_ids'])->get();
        $count = $users->count();

        switch ($validated['action']) {
            case 'activate':
                User::whereIn('id', $validated['user_ids'])->update(['is_active' => true]);
                $message = "{$count} pengguna berhasil diaktifkan.";
                $action = 'bulk_activate';
                break;

            case 'deactivate':
                User::whereIn('id', $validated['user_ids'])->update(['is_active' => false]);
                $message = "{$count} pengguna berhasil dinonaktifkan.";
                $action = 'bulk_deactivate';
                break;

            case 'delete':
                User::whereIn('id', $validated['user_ids'])->delete();
                $message = "{$count} pengguna berhasil dihapus.";
                $action = 'bulk_delete';
                break;

            case 'reset_password':
                $passwords = [];
                foreach ($users as $user) {
                    $newPass = Str::random(10);
                    $user->update(['password' => Hash::make($newPass)]);
                    $passwords[$user->username] = $newPass;
                }
                $message = "{$count} password pengguna berhasil direset.";
                $action = 'bulk_reset_password';
                session()->flash('reset_passwords', $passwords);
                break;
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $message . " (IDs: " . implode(', ', $validated['user_ids']) . ")",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', $message);
    }

    /**
     * Export users to CSV.
     */
    public function export(Request $request)
    {
        $query = User::query();

        // Apply same filters as index
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status'));
        }

        $users = $query->get();

        $filename = 'users_export_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['ID', 'Username', 'Email', 'Nama Lengkap', 'Role', 'Kelas', 'Sekolah', 'Status', 'Artikel', 'Terakhir Login', 'Bergabung']);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->username,
                    $user->email,
                    $user->full_name,
                    $user->role,
                    $user->class ?? '-',
                    $user->school_name ?? '-',
                    $user->is_active ? 'Aktif' : 'Nonaktif',
                    $user->articles()->count(),
                    $user->last_login_at?->format('d/m/Y H:i') ?? '-',
                    $user->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'export_users',
            'description' => "Mengekspor data {$users->count()} pengguna",
            'ip_address' => $request->ip(),
        ]);

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get user data for AJAX.
     */
    public function getData(User $user): JsonResponse
    {
        $user->load([
            'articles' => function ($q) {
                $q->latest()->limit(5);
            },
            'comments' => function ($q) {
                $q->with('article')->latest()->limit(5);
            },
            'activityLogs' => function ($q) {
                $q->latest()->limit(10);
            }
        ]);

        return response()->json([
            'user' => $user,
            'stats' => [
                'articles' => $user->articles()->count(),
                'published' => $user->articles()->where('status', 'published')->count(),
                'comments' => $user->comments()->count(),
                'views' => $user->articles()->sum('views_count'),
            ],
            'recent_articles' => $user->articles,
            'recent_comments' => $user->comments,
            'activity_logs' => $user->activityLogs,
        ]);
    }
    /**
     * Send notification to user.
     */
    public function sendNotification(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        \App\Models\Notification::create([
            'user_id' => $user->id,
            'type' => 'admin_notification',
            'title' => $validated['title'],
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'send_notification',
            'description' => "Mengirim notifikasi ke {$user->full_name}: {$validated['title']}",
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Notifikasi berhasil dikirim.');
    }
}
