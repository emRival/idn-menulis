# IDN Menulis - Detailed Implementation Guide

## 📖 Complete Developer Reference

Panduan lengkap untuk memahami, mengembangkan, dan meng-extend aplikasi IDN Menulis.

---

## 🏗️ ARCHITECTURE OVERVIEW

### Layered Architecture
```
┌─────────────────────────────────┐
│   Views (Blade Templates)        │  ← User Interface
├─────────────────────────────────┤
│   Controllers                    │  ← Request Handler
├─────────────────────────────────┤
│   Services & Business Logic      │  ← Core Logic
├─────────────────────────────────┤
│   Models (Eloquent ORM)          │  ← Data Access
├─────────────────────────────────┤
│   MySQL Database                 │  ← Data Storage
└─────────────────────────────────┘

Cross-cutting:
- Middleware (auth, tracking)
- Policies (authorization)
- Form Requests (validation)
- Console Commands (scheduling)
```

### Request Flow Example: Creating an Article
```
1. User submits form in create.blade.php
2. POST /articles → ArticleController@store()
3. Middleware checks auth & role
4. ArticleRequest validates input
5. ArticleService (business logic):
   - Generate slug & check collision
   - Calculate reading time
   - Upload & optimize image
   - Create article record
   - Attach tags
6. Return view/redirect with flash message
7. Blade template displays success/error
```

---

## 📚 MODEL RELATIONSHIPS (Complete Reference)

### User Model
```php
// Relations
hasMany(Article)      // Articles written by user
hasMany(Comment)      // Comments posted by user
hasMany(ArticleApproval) // Reviews (if guru/admin)
hasMany(Notification) // User's notifications
hasMany(ActivityLog)  // User's activities
belongsToMany(Article) // via likes table
belongsToMany(Article) // via bookmarks table

// Scopes
->where('role', 'admin')  // Useful in queries
->where('is_active', true)

// Useful Methods
$user->isAdmin()      // true if role == 'admin'
$user->isGuru()       // true if role == 'guru'
$user->isSiswa()      // true if role == 'siswa'
$user->updateLastLogin()
```

### Article Model
```php
// Relations
belongsTo(User, 'user_id')           // Author
belongsTo(Category, 'category_id')   // Category
hasMany(Comment)                     // Comments on article
hasMany(ArticleApproval)             // Approval history
belongsToMany(Tag)                   // Tags
belongsToMany(User, 'likes', 'article_id', 'user_id') // Who liked

// Scopes
->published()        // Only status='published' & published_at <= now
->byCategory($id)    // Filter by category
->byAuthor($id)      // Filter by author
->search($keyword)   // FULLTEXT search

// Useful Methods
$article->isPublished()           // bool
$article->isPending()            // bool
$article->isDraft()              // bool
$article->isScheduled()          // bool
$article->isLikedBy($user)      // bool
$article->isBookmarkedBy($user) // bool
$article->incrementViews()      // Increment views_count
$article->calculateReadingTime() // Calculate & save reading_time
```

### Category Model
```php
// Relations
hasMany(Article)

// Scopes
->active() // where('is_active', true)

// Useful
Categories displayed in navbar, homepage, filters
```

### Tag Model
```php
// Relations
belongsToMany(Article)

// Methods
$tag->incrementUsage()  // Increase usage_count
$tag->decrementUsage()  // Decrease usage_count

// Useful
Tags displayed in articles, sidebar, filter options
```

### Comment Model
```php
// Relations
belongsTo(Article)
belongsTo(User)
belongsTo(Comment, 'parent_id') // Parent comment (null if top-level)
hasMany(Comment, 'parent_id')   // Replies

// Scopes
->approved()   // where('is_approved', true)
->topLevel()   // where('parent_id', null)

// Methods
$comment->isReply()     // bool - true if parent_id != null
$comment->allReplies()  // Get all nested replies
```

### Like, Bookmark, ArticleApproval, Notification
```php
// Like: user can like only once per article (unique constraint)
// Bookmark: user can bookmark only once per article
// ArticleApproval: track approval history
// Notification: track user notifications with read status
```

---

## 🔌 CONTROLLER STRUCTURE

### Basic Controller Pattern
```php
class ArticleController extends Controller
{
    public function __construct()
    {
        // Apply middleware to specific methods
        $this->middleware('auth')->except(['show', 'index']);
        $this->middleware('check-role:guru,admin')->only(['approve']);
    }

    // Step 1: Get request
    public function store(ArticleRequest $request)
    {
        // Step 2: Use service for business logic
        $article = $this->articleService->createArticle(
            $request->validated()
        );

        // Step 3: Return response
        return redirect()->route('articles.show', $article)
            ->with('success', 'Article created successfully');
    }

    // Authorization check
    public function update(Article $article, ArticleRequest $request)
    {
        $this->authorize('update', $article); // Policy check
        // Process update...
    }
}
```

### Common Patterns

**Query & Return JSON:**
```php
public function trending()
{
    $articles = Cache::remember('trending_articles', 30*60, function() {
        return Article::published()
            ->orderByDesc('views_count')
            ->limit(5)
            ->get();
    });
    
    return response()->json($articles);
}
```

**Form with Flash Message:**
```php
public function store(CreateRequest $request)
{
    $record = Model::create($request->validated());
    
    return back()->with('success', 'Created successfully!');
}
```

**Handle Error:**
```php
try {
    // operation
} catch (Exception $e) {
    return back()->with('error', 'Something went wrong');
}
```

---

## 🎨 BLADE TEMPLATE PATTERNS

### Pattern 1: Master Layout Extends
```blade
@extends('layouts.app')

@section('title', 'Page Title - IDN Menulis')

<div class="container mx-auto px-4 py-8">
    @include('components.breadcrumb')
    
    <h1 class="text-3xl font-bold mb-6">Page Title</h1>
    
    <!-- Content here -->
</div>

@endsection
```

### Pattern 2: Grid Layout (Articles, etc)
```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($articles as $article)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
            <img src="{{ $article->featured_image }}" 
                 class="w-full h-48 object-cover rounded-t-lg">
            
            <div class="p-4">
                <h3 class="font-bold text-lg mb-2">{{ $article->title }}</h3>
                <p class="text-gray-600 text-sm mb-4">{{ $article->excerpt }}</p>
                
                <div class="flex justify-between text-xs text-gray-500">
                    <span>{{ $article->views_count }} views</span>
                    <span>{{ $article->reading_time }} min read</span>
                </div>
                
                <a href="{{ route('articles.show', $article) }}" 
                   class="mt-4 inline-block text-blue-600 hover:underline">
                    Read More →
                </a>
            </div>
        </div>
    @empty
        <p class="text-center text-gray-500">No articles found</p>
    @endforelse
</div>
```

### Pattern 3: Form with Validation
```blade
<form method="POST" action="{{ route('articles.store') }}" 
      enctype="multipart/form-data">
    @csrf
    
    <div class="mb-4">
        <label for="title" class="block font-medium mb-1">Title</label>
        <input type="text" name="title" id="title" 
               value="{{ old('title') }}"
               class="w-full px-4 py-2 border rounded 
                      @error('title') border-red-500 @enderror">
        @error('title')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    
    <div class="mb-4">
        <label for="content" class="block font-medium mb-1">Content</label>
        <textarea id="content" name="content" class="tinymce-editor">
            {{ old('content') }}
        </textarea>
        @error('content')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">
        Save
    </button>
</form>
```

### Pattern 4: Alpine.js Interaction
```blade
<div x-data="{ 
    open: false,
    liked: {{ $article->isLikedBy(auth()->user()) ? 'true' : 'false' }},
    likeCount: {{ $article->likes()->count() }}
}">
    <!-- Dropdown -->
    <div class="relative">
        <button @click="open = !open" class="px-4 py-2 bg-gray-100 rounded">
            {{ auth()->user()->name }} ▼
        </button>
        
        <div x-show="open" class="absolute bg-white shadow mt-1 rounded">
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
        </div>
    </div>
    
    <!-- Like button with AJAX -->
    <button @click="toggleLike()" 
            :class="liked ? 'text-red-600' : 'text-gray-400'"
            class="px-4 py-2 rounded transition">
        ♥ <span x-text="likeCount"></span>
    </button>
</div>

<script>
function toggleLike() {
    this.$data.liked = !this.$data.liked;
    
    fetch(`/api/articles/{{ $article->id }}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        this.$data.likeCount = data.count;
    });
}
</script>
```

### Pattern 5: Conditional Rendering
```blade
@auth
    <!-- Show for authenticated users -->
    <p>Welcome, {{ auth()->user()->name }}</p>
    
    @if(auth()->user()->isAdmin())
        <!-- Admin panel link -->
        <a href="{{ route('admin.dashboard') }}">Admin Panel</a>
    @elseif(auth()->user()->isGuru())
        <!-- Guru options -->
        <a href="{{ route('approvals.pending') }}">Pending Approvals</a>
    @endif
@else
    <!-- Show for guests -->
    <a href="{{ route('login') }}">Login</a>
@endauth
```

### Pattern 6: Pagination
```blade
<div class="grid grid-cols-3 gap-6">
    @foreach($articles as $article)
        <!-- Article card -->
    @endforeach
</div>

<div class="mt-8">
    {{ $articles->links() }}
</div>
```

---

## ✅ VALIDATION RULES

### ArticleRequest
```php
[
    'title' => 'required|string|min:10|max:255|unique:articles',
    'category_id' => 'required|exists:categories,id',
    'content' => 'required|string|min:300',
    'excerpt' => 'nullable|string|max:500',
    'featured_image' => 'nullable|image|max:2048',
    'tags' => 'nullable|array|max:5',
    'tags.*' => 'exists:tags,id',
    'scheduled_at' => 'nullable|date|after:now',
]
```

### CommentRequest
```php
[
    'content' => 'required|string|min:5|max:1000',
    'parent_id' => 'nullable|exists:comments,id',
]
```

### UserRequest
```php
[
    'username' => 'required|alpha_dash|min:4|max:50|unique:users',
    'email' => 'required|email|unique:users',
    'full_name' => 'required|string|min:3|max:100',
    'school_name' => 'nullable|string',
    'class' => 'required_if:role,siswa|string',
    'role' => 'required|in:admin,guru,siswa',
    'is_active' => 'boolean',
]
```

---

## 🔐 AUTHORIZATION (Policies)

### ArticlePolicy
```php
// In controller: $this->authorize('view', $article);
// Or in blade: @can('update', $article)

public function view(User $user, Article $article)
{
    // Anyone can view published articles
    if ($article->isPublished()) {
        return true;
    }
    
    // Author can view own article
    return $user->id === $article->user_id;
}

public function update(User $user, Article $article)
{
    // Only author can update
    if ($user->id !== $article->user_id) {
        return false;
    }
    
    // Can only edit draft or rejected articles
    return in_array($article->status, ['draft', 'rejected']);
}

public function publish(User $user, Article $article)
{
    // Only admin/guru can publish
    return $user->isAdmin() || $user->isGuru();
}
```

---

## 📊 USING SERVICES

### ArticleService Example
```php
// In controller
$this->articleService->createArticle([
    'title' => $request->title,
    'content' => $request->content,
    'category_id' => $request->category_id,
    'tags' => $request->tags,
    'user_id' => auth()->id(),
    'featured_image' => $request->featured_image,
    'scheduled_at' => $request->scheduled_at,
]);

// Service handles:
// 1. Generate unique slug with collision detection
// 2. Calculate reading time
// 3. Upload & optimize featured image
// 4. Create article
// 5. Attach/sync tags
// 6. Set correct status (draft/pending/scheduled)
```

### ImageService Example
```php
// Upload article image
$url = $this->imageService->uploadArticleImage(
    $request->file('image'),
    'articles'
);

// Upload avatar
$url = $this->imageService->uploadAvatar(
    $request->file('avatar'),
    auth()->user()->id
);
```

### NotificationService Example
```php
// Send custom notification
$this->notificationService->send(
    $user->id,
    'article_approved',
    'Your article was approved!',
    route('articles.show', $article)
);

// Specific notifications
$this->notificationService->sendArticleApproved($article);
$this->notificationService->sendArticleRejected($article, 'Needs more citations');
$this->notificationService->sendCommentNotification($comment);
```

---

## 🎯 COMMON TASKS

### Task 1: Get All Articles with User & Category
```php
$articles = Article::with(['user', 'category', 'tags'])
    ->where('status', 'published')
    ->orderByDesc('published_at')
    ->paginate(15);
```

### Task 2: Search Articles with Multiple Filters
```php
$articles = Article::published()
    ->when($request->category, function ($query) use ($request) {
        $query->where('category_id', $request->category);
    })
    ->when($request->search, function ($query) use ($request) {
        $query->whereFullText(['title', 'content', 'excerpt'], $request->search);
    })
    ->when($request->tag, function ($query) use ($request) {
        $query->whereHas('tags', function ($q) use ($request) {
            $q->where('id', $request->tag);
        });
    })
    ->paginate(20);
```

### Task 3: Get User's Statistics
```php
$stats = [
    'articles' => $user->articles()->count(),
    'views' => $user->articles()->sum('views_count'),
    'likes' => Like::whereIn('article_id', $user->articles()->pluck('id'))->count(),
    'comments' => $user->comments()->count(),
];
```

### Task 4: Create Approval Workflow
```php
// Step 1: User submits article (ArticleController@store)
$article = Article::create([
    'status' => 'pending', // Set to pending for approval
    'user_id' => auth()->id(),
    // ...
]);

// Step 2: Guru reviews (ApprovalController@show & approve/reject)
// Stores record in article_approvals table
// Sends notification to user

// Step 3: Notification triggers email (if using queue)
// User sees approval status in dashboard
```

### Task 5: Schedule Article Publishing
```php
$article = Article::create([
    'status' => 'scheduled',
    'scheduled_at' => $request->scheduled_at, // Future date
    // ...
]);

// Console command runs every 5 minutes:
// PublishScheduledArticles checks for articles where scheduled_at <= now()
// Changes status to 'published' & sets published_at
// Notification sent to author
```

---

## 🧪 TESTING CHECKLIST

### Manual Testing (Before Production)
- [ ] User registration & email verification
- [ ] Login with different roles (admin, guru, siswa)
- [ ] Create article as siswa (should be pending)
- [ ] Guru approves/rejects article
- [ ] Search articles with FULLTEXT
- [ ] Like/unlike article
- [ ] Bookmark article
- [ ] Add comment & reply to comment
- [ ] Edit own profile
- [ ] Admin manage users, categories, tags
- [ ] Scheduled article publishes at correct time
- [ ] Weekly digest email received
- [ ] Responsive design on mobile

### Unit Tests
```php
// Test model relationships
public function test_user_has_many_articles()
{
    $user = User::factory()->create();
    $article = Article::factory()->for($user)->create();
    
    $this->assertTrue($user->articles->contains($article));
}

// Test validation
public function test_article_requires_title()
{
    $response = $this->post('/articles', [
        'content' => 'Lorem ipsum dolor sit amet...',
    ]);
    
    $response->assertHasErrors('title');
}

// Test authorization
public function test_user_cannot_edit_others_article()
{
    $user = User::factory()->create();
    $article = Article::factory()->create();
    
    $this->actingAs($user)->put("/articles/{$article->id}", [])
        ->assertForbidden();
}
```

---

## 🚀 DEPLOYMENT CHECKLIST

### Pre-Deployment
- [ ] Review CODE_REVIEW.md
- [ ] Run tests: `php artisan test`
- [ ] Check linting: `./vendor/bin/pint`
- [ ] Database backup
- [ ] Update CHANGELOG.md

### Environment Setup
- [ ] Configure .env for production
- [ ] Generate app key: `php artisan key:generate`
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Optimize: `php artisan optimize`
- [ ] Cache config: `php artisan config:cache`

### After Deployment
- [ ] Test login on production
- [ ] Monitor error logs: `tail -f storage/logs/laravel.log`
- [ ] Test scheduled tasks: `php artisan schedule:work`
- [ ] Verify email sending
- [ ] Test file uploads

---

## 📞 SUPPORT & RESOURCES

- **Laravel Documentation:** https://laravel.com/docs/11.x
- **Eloquent Guide:** https://laravel.com/docs/11.x/eloquent
- **Blade Templating:** https://laravel.com/docs/11.x/blade
- **Validation:** https://laravel.com/docs/11.x/validation
- **Authorization (Policies):** https://laravel.com/docs/11.x/authorization
- **Middleware:** https://laravel.com/docs/11.x/middleware
- **Scheduling:** https://laravel.com/docs/11.x/scheduling

---

**Document Version:** 1.0
**Last Updated:** February 11, 2026
**Maintained By:** Development Team
