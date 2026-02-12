# IDN Menulis - API Reference & Routes

## 📡 Complete API Documentation

Dokumentasi lengkap semua endpoints, request/response formats, dan contoh penggunaan.

---

## 🗺️ ROUTES OVERVIEW

### Public Routes (No Authentication Required)
```
GET  /                           HomeController@index
GET  /categories/{id}            HomeController@category
GET  /tags/{id}                  HomeController@tag
GET  /search                     HomeController@search
GET  /articles/{article}         ArticleController@show
```

### Authenticated Routes (Requires User Login)
```
GET    /dashboard               DashboardController@index
GET    /articles/create         ArticleController@create
POST   /articles                ArticleController@store
GET    /articles/{article}/edit ArticleController@edit
PUT    /articles/{article}      ArticleController@update
DELETE /articles/{article}      ArticleController@destroy
POST   /articles/{article}/image ArticleController@uploadImage
POST   /articles/{article}/publish ArticleController@publish
POST   /articles/{article}/schedule ArticleController@schedule
POST   /articles/{article}/revert-to-draft ArticleController@revertToDraft

POST   /articles/{article}/comments ArticleController@store (CommentController)
PUT    /comments/{comment}      CommentController@update
DELETE /comments/{comment}      CommentController@destroy

POST   /articles/{id}/like      LikeController@toggle
GET    /api/articles/{id}/likes LikeController@count

POST   /articles/{id}/bookmark  BookmarkController@toggle
GET    /bookmarks               BookmarkController@myBookmarks

GET    /profile                 ProfileController@show
GET    /profile/edit            ProfileController@edit
PUT    /profile                 ProfileController@update
GET    /profile/password        ProfileController@editPassword
PUT    /profile/password        ProfileController@updatePassword
POST   /profile/delete          ProfileController@delete

GET    /notifications           NotificationController@index
POST   /notifications/{id}/read NotificationController@markAsRead
```

### Guru/Admin Routes (Requires Guru or Admin Role)
```
GET    /approvals/pending       ApprovalController@pending
GET    /approvals/{article}     ApprovalController@show
POST   /approvals/{article}/approve ApprovalController@approve
POST   /approvals/{article}/reject  ApprovalController@reject
GET    /approvals/history       ApprovalController@history

GET    /comments/pending        CommentController@pending
POST   /comments/{comment}/approve CommentController@approve
POST   /comments/{comment}/reject   CommentController@reject
```

### Admin Routes (Requires Admin Role)
```
GET    /admin/users             Admin\UserController@index
GET    /admin/users/{user}      Admin\UserController@show
GET    /admin/users/{user}/edit Admin\UserController@edit
PUT    /admin/users/{user}      Admin\UserController@update
POST   /admin/users/{user}/activate Admin\UserController@activate
POST   /admin/users/{user}/deactivate Admin\UserController@deactivate
DELETE /admin/users/{user}      Admin\UserController@destroy

GET    /admin/categories        Admin\CategoryController@index
GET    /admin/categories/create Admin\CategoryController@create
POST   /admin/categories        Admin\CategoryController@store
GET    /admin/categories/{id}/edit Admin\CategoryController@edit
PUT    /admin/categories/{id}   Admin\CategoryController@update
DELETE /admin/categories/{id}   Admin\CategoryController@destroy

GET    /admin/tags              Admin\TagController@index
GET    /admin/tags/create       Admin\TagController@create
POST   /admin/tags              Admin\TagController@store
GET    /admin/tags/{id}/edit    Admin\TagController@edit
PUT    /admin/tags/{id}         Admin\TagController@update
DELETE /admin/tags/{id}         Admin\TagController@destroy

GET    /admin/activity-logs     Admin\ActivityLogController@index
GET    /admin/dashboard         DashboardController@adminDashboard
GET    /admin/settings          SettingsController@index
```

---

## 📝 DETAILED ENDPOINT DOCUMENTATION

### 1. HOME PAGE
```
GET /
Author: HomeController@index
Auth: Not required
```

**Response:** Renders home/index.blade.php with:
- Featured articles (3 items)
- All categories (2x2 grid)
- Latest articles (paginated, 12 per page)
- Popular articles (sidebar)
- Tags cloud

---

### 2. CREATE ARTICLE
```
GET /articles/create
Author: ArticleController@create
Auth: Required (any user)
```

**Response:** Renders articles/create.blade.php with:
- Title input (min 10, max 255 chars)
- Category dropdown
- Content editor (TinyMCE)
- Featured image upload
- Excerpt textarea (max 500)
- Tags multiselect
- Schedule datetime (optional)
- Action buttons: Draft, Submit

---

### 3. STORE ARTICLE
```
POST /articles
Author: ArticleController@store
Auth: Required (any user)

Request:
{
    "title": "Lorem Ipsum Dolor",
    "category_id": 1,
    "content": "<p>Long content...</p>",
    "excerpt": "Short summary",
    "featured_image": [File],
    "tags": [1, 2, 3],
    "scheduled_at": "2026-02-20 10:00" (optional)
}

Response:
- Success: Redirect to article.show with flash message
- Error: Back to create.blade with validation errors
```

**Business Logic:**
1. Generate unique slug from title (with collision detection)
2. Calculate reading time (200 words/minute estimate)
3. Upload & optimize featured image:
   - Resize to 1200x630px
   - Convert to WebP format
   - Store in storage/app/public/articles/
4. Determine status:
   - If scheduled_at set → status='scheduled'
   - If user is admin/guru → status='published'
   - Else → status='pending' (needs approval)
5. Create article record
6. Attach tags via pivot table
7. Create activity log entry
8. Send notification if pending approval

---

### 4. VIEW ARTICLE (Show)
```
GET /articles/{article}
Example: GET /articles/first-article-about-writing
Author: ArticleController@show
Auth: Not required
```

**Response:** Renders articles/show.blade.php with:
- Article metadata (author, date, reading time, views)
- Featured image
- Content (rendered with prose styling)
- Tags as clickable links
- Like button (shows count, AJAX toggle if auth)
- Bookmark button (shows count, AJAX toggle if auth)
- Comments section:
  - Comment form (if authenticated)
  - Comments list (paginated)
  - Nested replies (max depth 2)
  - Moderation indicators (if user is moderator)
- Related articles (3 items from same category)

**Side Effects:**
- Increment views_count
- Log activity: "User viewed article"

---

### 5. EDIT & UPDATE ARTICLE
```
GET /articles/{article}/edit
Author: ArticleController@edit
Auth: Required
Authorization: Owner or Admin

PUT /articles/{article}
Author: ArticleController@update
Auth: Required
Authorization: Owner or Admin

Request:
{
    "title": "Updated Title",
    "category_id": 1,
    "content": "Updated content",
    "tags": [1, 2],
    "featured_image": [File optional],
    "scheduled_at": null
}

Response:
- Success: Redirect to article.show with ✓ message
- Error: Back to edit with validation errors
- Forbidden: Redirect with error message
```

**Rules:**
- Can only edit if status is 'draft' or 'rejected'
- Published/scheduled articles locked
- Recalculates reading time on update
- Re-optimizes image if new featured_image provided
- Re-syncs tags

---

### 6. PUBLISH ARTICLE
```
POST /articles/{article}/publish
Author: ArticleController@publish
Auth: Required
Authorization: Author (if draft) OR Admin/Guru

Request:
{
    "scheduled_at": null (optional)
}

Response:
{
    "success": true,
    "message": "Article published successfully",
    "article": { ... }
}
```

**States:**
1. Draft → Published (immediately)
2. Draft → Scheduled (at specified datetime)
3. Pending (after guru approval) → Published (immediately)

**Side Effects:**
- Set status='published' & published_at=now()
- Create ArticleApproval record (if from pending)
- Send notification to author
- Update activity log
- Clear trending cache

---

### 7. REJECT ARTICLE
```
POST /approvals/{article}/reject
Author: ApprovalController@reject
Auth: Required
Authorization: Guru or Admin

Request:
{
    "notes": "Needs more citations and better grammar"
}

Response:
{
    "success": true,
    "message": "Article rejected successfully",
    "article": { ... }
}
```

**Process:**
1. Set article status='rejected'
2. Store rejection notes in article_approvals
3. Send notification to author with reason
4. Author can revise and resubmit

---

### 8. LIKE ARTICLE (AJAX)
```
POST /articles/{id}/like
Author: LikeController@toggle
Auth: Required
Content-Type: application/json
Header: X-CSRF-TOKEN

Response:
{
    "success": true,
    "liked": true/false,
    "count": 42
}
```

**Logic:**
- If user already liked → unlike & return liked=false
- Else → like & return liked=true
- Return total like count
- Update activity log

---

### 9. BOOKMARK ARTICLE (AJAX)
```
POST /articles/{id}/bookmark
Author: BookmarkController@toggle
Auth: Required

Response:
{
    "success": true,
    "bookmarked": true/false
}
```

**Logic:**
- If user already bookmarked → remove bookmark
- Else → add bookmark
- Return status

---

### 10. COMMENT ON ARTICLE
```
POST /articles/{article}/comments
Author: CommentController@store
Auth: Required
Authorization: User must be active

Request:
{
    "content": "Great article! I learned a lot.",
    "parent_id": null  // Set if replying to comment
}

Response:
{
    "success": true,
    "message": "Comment posted successfully",
    "comment": { ... }
}
```

**Rules:**
- Content min 5, max 1000 characters
- Max nesting depth: 2 (comment → reply → no deeper)
- Auto-approved if user is guru/admin
- Pending approval if regular user
- Send notification to article author
- Send notification to parent comment author (if reply)

---

### 11. SEARCH ARTICLES
```
GET /search?q=writing&category=1&tag=2&sort=latest
Author: HomeController@search
Auth: Not required

Query Parameters:
- q: Search keyword (FULLTEXT search in title, content, excerpt)
- category: Filter by category_id
- tag: Filter by tag_id
- sort: 'latest' | 'popular' | 'trending' (default: relevance)
- page: Pagination (default: 1)

Response: Renders search/results.blade.php with:
- Search keyword highlighted
- Filter indicators
- Matching articles (20 per page)
- Pagination
- Sidebar with filter options
```

**Search Implementation:**
```php
Article::published()
    ->whereFullText(['title', 'content', 'excerpt'], $keyword)
    ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
    ->when($tagId, fn($q) => $q->whereHas('tags', fn($q2) => $q2->where('id', $tagId)))
    ->when($sort === 'popular', fn($q) => $q->orderByDesc('views_count'))
    ->paginate(20)
```

---

### 12. USER DASHBOARD
```
GET /dashboard
Author: DashboardController@index
Auth: Required
```

**Response:** Route to role-specific dashboard:

```php
if (auth()->user()->isAdmin()) {
    return View::make('dashboard.admin', [
        'users_count' => User::count(),
        'articles_count' => Article::count(),
        'recent_activities' => ActivityLog::latest()->limit(10)->get(),
        'analytics' => [
            'daily_users' => [...],
            'monthly_articles' => [...],
        ]
    ]);
}
```

**Siswa Dashboard:**
- Personal stats (articles, views, likes, comments)
- Chart: My article views over 30 days
- Recent articles list
- Draft articles list

**Guru Dashboard:**
- Pending approvals count
- Approval history (last 20)
- Top 10 authors
- Statistics

**Admin Dashboard:**
- Full analytics dashboard
- User statistics
- Article statistics
- Recent activities timeline
- Quick actions

---

### 13. USER PROFILE
```
GET /profile
Author: ProfileController@show
Auth: Required

Response: Renders profile/show.blade.php with:
- User avatar
- Username, full name, email
- School name, class (if siswa)
- Bio
- Statistics:
  - Articles count
  - Comments count
  - Likes received count
- Recent articles
- Edit button
```

---

### 14. UPDATE PROFILE
```
PUT /profile
Author: ProfileController@update
Auth: Required

Request:
{
    "full_name": "John Doe",
    "bio": "Writer and educator",
    "avatar": [File optional],
    "school_name": "SMA Negeri 1",
    "class": "XII-A"
}

Response: Redirect to /profile with success message
```

---

### 15. ADMIN: LIST USERS
```
GET /admin/users?search=john&role=siswa&status=active
Author: Admin\UserController@index
Auth: Required (Admin only)

Query Parameters:
- search: Search username/email/full_name
- role: Filter by 'admin' | 'guru' | 'siswa'
- status: Filter by 'active' | 'inactive'
- page: Pagination

Response: Renders admin/users/index.blade.php with:
- Users table:
  - Username, email, full name
  - Role badge
  - Status (active/inactive)
  - Created at
  - Actions: view, edit, activate/deactivate, delete
- Bulk actions: Delete, Change role, Activate, Deactivate
```

---

### 16. ADMIN: CREATE CATEGORY
```
GET /admin/categories/create
Author: Admin\CategoryController@create
Auth: Required (Admin only)

Response: Renders form with fields:
- Name (required, unique)
- Description
- Icon (select from list)
- Color (color picker)
- Order position (number)
- Is active (toggle)
```

---

### 17. TRENDING ARTICLES (API)
```
GET /api/trending
Author: ArticleController@trending
Auth: Not required
Caching: 30 minutes

Response:
[
    {
        "id": 1,
        "title": "Top article",
        "views_count": 1200,
        "slug": "top-article",
        "featured_image": "..."
    },
    ...
]

Response Example:
{
    "articles": [
        {
            "id": 1,
            "title": "10 Tips untuk Menulis Lebih Baik",
            "views_count": 5000,
            "user": {
                "id": 2,
                "username": "john_doe"
            },
            "category": {
                "id": 1,
                "name": "Tips & Trik"
            }
        },
        ...
    ]
}
```

**Cached Implementation:**
```php
Cache::remember('trending_articles', 30*60, function() {
    return Article::published()
        ->orderByDesc('views_count')
        ->with(['user', 'category'])
        ->limit(5)
        ->get();
});
```

---

## 🔒 AUTHENTICATION & AUTHORIZATION

### Middleware Stack
```php
// Applied globally to all routes
- \App\Http\Middleware\EncryptCookies
- \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode
- \Illuminate\Foundation\Http\Middleware\ValidatePostSize
- \Illuminate\Foundation\Http\Middleware\TrimStrings
- \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull

// Route middleware (apply per route/group)
- 'auth' => \App\Http\Middleware\Authenticate
- 'check-role' => \App\Http\Middleware\CheckRole
- 'track-activity' => \App\Http\Middleware\TrackActivity
- 'update-login' => \App\Http\Middleware\UpdateLastLogin
```

### Authorization Policies
```php
// Article Policy
Gate::define('view-article', function (User $user, Article $article) {
    return $article->isPublished() || $user->id === $article->user_id;
});

// In controller
$this->authorize('update', $article);

// In blade
@can('update', $article)
    <a href="{{ route('articles.edit', $article) }}">Edit</a>
@endcan
```

---

## 💾 RESPONSE FORMATS

### Success Response
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {
        // Resource data here
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "title": ["Title is required"],
        "content": ["Content must be at least 300 characters"]
    }
}
```

### Paginated Response
```json
{
    "data": [...],
    "links": {
        "first": "http://example.com/articles?page=1",
        "last": "http://example.com/articles?page=10",
        "prev": "http://example.com/articles?page=2",
        "next": "http://example.com/articles?page=4"
    },
    "meta": {
        "current_page": 3,
        "from": 41,
        "last_page": 10,
        "per_page": 20,
        "to": 60,
        "total": 200
    }
}
```

---

## 🧪 TESTING WITH CURL

### Search Articles
```bash
curl -X GET "http://localhost:8000/search?q=writing" \
  -H "Accept: application/json"
```

### Create Article (Requires Auth)
```bash
curl -X POST "http://localhost:8000/articles" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: <token>" \
  -d '{
    "title": "My First Article",
    "category_id": 1,
    "content": "Article content goes here...",
    "tags": [1, 2, 3]
  }'
```

### Like Article (AJAX)
```bash
curl -X POST "http://localhost:8000/articles/1/like" \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: <token>" \
  -H "X-Requested-With: XMLHttpRequest"
```

### Get Trending Articles
```bash
curl -X GET "http://localhost:8000/api/trending" \
  -H "Accept: application/json"
```

---

## 📋 STATUS CODES

- **200** OK - Request successful
- **201** Created - Resource created successfully
- **204** No Content - Request successful, no content to return
- **400** Bad Request - Invalid request data
- **401** Unauthorized - Not authenticated
- **403** Forbidden - Authenticated but not authorized
- **404** Not Found - Resource not found
- **422** Unprocessable Entity - Validation failed
- **500** Internal Server Error - Server error

---

## 🔗 RELATED ENDPOINTS

- **Article Edit** → Needs ArticleRequest validation
- **Comment Creation** → Sends notification to article author
- **Article Publish** → Updates status & creates ArticleApproval
- **Like Article** → Updates views_count & activity log
- **Bookmark** → Updates user's bookmark collection

---

**API Version:** 1.0
**Last Updated:** February 11, 2026
**Status:** Production Ready
