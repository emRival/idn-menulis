<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Like;
use App\Services\ImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(private ImageService $imageService) {}

    /**
     * Display user profile.
     */
    public function show(): View
    {
        $user = Auth::user();

        // Load user stats
        $stats = $this->getUserStats($user);

        // Load recent articles
        $articles = $user->articles()
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        // Load bookmarked articles
        $bookmarks = $user->bookmarks()
            ->with(['user', 'category'])
            ->latest('bookmarks.created_at')
            ->take(5)
            ->get();

        // Load recent comments
        $comments = $user->comments()
            ->with('article')
            ->latest()
            ->take(5)
            ->get();

        // Calculate profile completion
        $profileCompletion = $this->calculateProfileCompletion($user);

        return view('profile.show', compact('user', 'stats', 'articles', 'bookmarks', 'comments', 'profileCompletion'));
    }

    /**
     * Get user statistics.
     */
    private function getUserStats($user): array
    {
        $articleIds = $user->articles()->pluck('id');

        return [
            'total_articles' => $user->articles()->count(),
            'published_articles' => $user->articles()->where('status', 'published')->count(),
            'pending_articles' => $user->articles()->where('status', 'pending')->count(),
            'draft_articles' => $user->articles()->where('status', 'draft')->count(),
            'total_views' => $user->articles()->sum('views_count'),
            'total_likes' => Like::whereIn('article_id', $articleIds)->count(),
            'total_comments' => Comment::whereIn('article_id', $articleIds)->count(),
            'total_bookmarks' => $user->bookmarks()->count(),
            'comments_made' => $user->comments()->count(),
        ];
    }

    /**
     * Calculate profile completion percentage.
     */
    private function calculateProfileCompletion($user): array
    {
        $fields = [
            'avatar' => !empty($user->avatar),
            'bio' => !empty($user->bio),
            'full_name' => !empty($user->full_name),
            'school_name' => !empty($user->school_name),
            'class' => !empty($user->class),
        ];

        $completed = count(array_filter($fields));
        $total = count($fields);
        $percentage = round(($completed / $total) * 100);

        return [
            'percentage' => $percentage,
            'completed' => $completed,
            'total' => $total,
            'fields' => $fields,
        ];
    }

    /**
     * Get articles for tab (AJAX).
     */
    public function getArticles(Request $request): JsonResponse
    {
        $user = Auth::user();
        $status = $request->get('status', 'all');

        $query = $user->articles()->with('category');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $articles = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'articles' => $articles,
        ]);
    }

    /**
     * Get bookmarks for tab (AJAX).
     */
    public function getBookmarks(): JsonResponse
    {
        $bookmarks = Auth::user()->bookmarks()
            ->with(['user', 'category'])
            ->latest('bookmarks.created_at')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'bookmarks' => $bookmarks,
        ]);
    }

    /**
     * Get comments for tab (AJAX).
     */
    public function getComments(): JsonResponse
    {
        $comments = Auth::user()->comments()
            ->with('article')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'comments' => $comments,
        ]);
    }

    /**
     * Update cover image.
     */
    public function updateCover(Request $request): JsonResponse
    {
        $request->validate([
            'cover_image' => 'required|image|max:10240|mimes:jpeg,png,jpg,webp', // 10MB, will be auto-compressed
        ]);

        $user = Auth::user();

        // Delete old cover if exists
        if ($user->cover_image) {
            Storage::disk('public')->delete($user->cover_image);
        }

        $path = $request->file('cover_image')->store('covers', 'public');
        $user->update(['cover_image' => $path]);

        return response()->json([
            'success' => true,
            'cover_url' => Storage::url($path),
        ]);
    }

    /**
     * Update avatar via AJAX.
     */
    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|max:10240|mimes:jpeg,png,jpg,webp', // 10MB, will be auto-compressed
        ]);

        $user = Auth::user();

        // Delete old avatar if exists
        if ($user->avatar) {
            $this->imageService->deleteFile($user->avatar);
        }

        $path = $this->imageService->uploadAvatar($request->file('avatar'));
        $user->update(['avatar' => $path]);

        return response()->json([
            'success' => true,
            'avatar_url' => Storage::url($path),
        ]);
    }

    /**
     * Show edit profile form.
     */
    public function edit(): View
    {
        $user = Auth::user();

        return view('profile.edit', compact('user'));
    }

    /**
     * Update profile.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                $this->imageService->deleteFile($user->avatar);
            }

            $data['avatar'] = $this->imageService->uploadAvatar($request->file('avatar'));
        }

        $user->update($data);

        return redirect()->route('profile.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Show change password form.
     */
    public function editPassword(): View
    {
        return view('profile.change-password');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
            'password_confirmation' => 'required',
        ]);

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        return redirect()->back()
            ->with('success', 'Password berhasil diubah.');
    }

    /**
     * Delete account.
     */
    public function delete(): RedirectResponse
    {
        $user = Auth::user();

        // Soft delete user
        $user->delete();

        Auth::logout();

        return redirect('/')
            ->with('success', 'Akun Anda berhasil dihapus.');
    }
}
