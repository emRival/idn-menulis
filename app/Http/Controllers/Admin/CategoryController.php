<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display list of categories.
     */
    public function index(Request $request): View
    {
        $query = Category::withCount('articles');

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Sort
        $sortBy = $request->get('sort', 'order_position');
        if ($sortBy === 'name') {
            $query->orderBy('name', 'asc');
        } elseif ($sortBy === 'articles') {
            $query->orderBy('articles_count', 'desc');
        } elseif ($sortBy === 'newest') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('order_position', 'asc');
        }

        $categories = $query->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total' => Category::count(),
            'active' => Category::where('is_active', true)->count(),
            'hidden' => Category::where('is_active', false)->count(),
            'popular' => Category::withCount('articles')->orderBy('articles_count', 'desc')->first(),
        ];

        return view('admin.categories.index', compact('categories', 'stats'));
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a new category.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'required|string|max:7',
            'order_position' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = true;
        $validated['order_position'] = $validated['order_position'] ?? (Category::max('order_position') + 1);

        $category = Category::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan.',
                'category' => $category->loadCount('articles'),
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Show category detail.
     */
    public function show(Category $category): View
    {
        $category->load('articles');

        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show edit form.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update category.
     */
    public function update(Request $request, Category $category): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'required|string|max:7',
            'order_position' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active') || $request->boolean('is_active');

        $category->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui.',
                'category' => $category->loadCount('articles'),
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Delete category.
     */
    public function destroy(Request $request, Category $category): RedirectResponse|JsonResponse
    {
        // Check if category has articles
        if ($category->articles()->count() > 0) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak dapat dihapus karena memiliki artikel.',
                ], 422);
            }
            return back()->with('error', 'Kategori tidak dapat dihapus karena memiliki artikel.');
        }

        $category->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus.',
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }

    /**
     * Toggle category status.
     */
    public function toggleStatus(Category $category): JsonResponse
    {
        $category->update(['is_active' => !$category->is_active]);

        return response()->json([
            'success' => true,
            'message' => $category->is_active ? 'Kategori diaktifkan.' : 'Kategori dinonaktifkan.',
            'is_active' => $category->is_active,
        ]);
    }

    /**
     * Bulk action on categories.
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id',
        ]);

        $action = $request->action;
        $ids = $request->ids;

        $count = 0;
        $errors = [];

        foreach ($ids as $id) {
            $category = Category::find($id);
            if (!$category) continue;

            if ($action === 'activate') {
                $category->update(['is_active' => true]);
                $count++;
            } elseif ($action === 'deactivate') {
                $category->update(['is_active' => false]);
                $count++;
            } elseif ($action === 'delete') {
                if ($category->articles()->count() > 0) {
                    $errors[] = $category->name;
                } else {
                    $category->delete();
                    $count++;
                }
            }
        }

        $message = match($action) {
            'activate' => "$count kategori berhasil diaktifkan.",
            'deactivate' => "$count kategori berhasil dinonaktifkan.",
            'delete' => "$count kategori berhasil dihapus.",
        };

        if (!empty($errors)) {
            $message .= ' Kategori berikut tidak dapat dihapus karena memiliki artikel: ' . implode(', ', $errors);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'count' => $count,
        ]);
    }

    /**
     * Reorder categories.
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:categories,id',
            'orders.*.position' => 'required|integer|min:0',
        ]);

        foreach ($request->orders as $order) {
            Category::where('id', $order['id'])->update(['order_position' => $order['position']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan kategori berhasil diperbarui.',
        ]);
    }

    /**
     * Get category data for editing.
     */
    public function getData(Category $category): JsonResponse
    {
        return response()->json([
            'success' => true,
            'category' => $category->loadCount('articles'),
        ]);
    }
}
