# IDN Menulis - Quick Reference & Cheat Sheet

## ⚡ Developer Quick Reference

Panduan cepat untuk perintah-perintah yang sering digunakan, patterns, dan tips.

---

## 🚀 QUICK START (5 MINUTES)

```bash
# 1. Setup environment
cp .env.example .env
php artisan key:generate

# 2. Database
mysql -u root -p -e "CREATE DATABASE idn_menulis"
php artisan migrate --seed

# 3. Install dependencies & compile assets
composer install
npm install && npm run dev

# 4. Start servers (in separate terminals)
php artisan serve              # Backend: http://localhost:8000
php artisan schedule:work      # Scheduler
npm run dev                    # Front-end dev server (Vite)

# 5. Login
# Email: admin1@menulis.id
# Password: password
```

---

## 📝 COMMON ARTISAN COMMANDS

### Database
```bash
php artisan migrate              # Run ALL migrations
php artisan migrate:rollback     # Rollback last batch
php artisan migrate:refresh      # Rollback + migrate fresh
php artisan migrate:refresh --seed  # Fresh + seed dummy data
php artisan db:seed              # Run seeders
php artisan db:seed --class=UserSeeder  # Run specific seeder
```

### Models & Migrations
```bash
php artisan make:model Article -mfpc  # Model + migration + factory + controller
php artisan make:migration create_articles_table
php artisan make:factory ArticleFactory
```

### Controllers & Routes
```bash
php artisan make:controller ArticleController --resource  # REST controller
php artisan route:list           # Show all routes
php artisan route:cache          # Cache routes (production)
```

### Request & Validation
```bash
php artisan make:request StoreArticleRequest
php artisan make:request ArticleRequest
```

### Middleware & Authorization
```bash
php artisan make:middleware CheckRole
php artisan make:policy ArticlePolicy
```

### Services & Commands
```bash
php artisan make:command PublishScheduledArticles  # Console command
php artisan make:event ArticlePublished
php artisan make:listener SendNotification
php artisan make:mail ArticleApprovedMail
```

### Testing
```bash
php artisan test                 # Run all tests
php artisan test --filter=UserTest  # Run specific test
php artisan tinker              # Interactive shell (REPL)
```

### Utilities
```bash
php artisan tinker              # Debug in interactive shell
php artisan optimize            # Cache everything (production)
php artisan config:cache        # Cache config files
php artisan cache:clear         # Clear all caches
php artisan storage:link        # Link storage/public → public/storage
```

---

## 🔄 COMMON CODE PATTERNS

### Get Resource with Relations
```php
// One-liner
$article = Article::with(['user', 'category', 'tags', 'comments'])->find($id);

// In controller
public function show(Article $article)
{
    return view('articles.show', [
        'article' => $article->load(['user', 'category', 'tags']),
        'likeCount' => $article->likes()->count(),
    ]);
}
```

### Filtered Query
```php
// With multiple filters
$articles = Article::published()
    ->when($request->category, fn($q) => $q->where('category_id', $request->category))
    ->when($request->search, fn($q) => $q->whereFullText(['title', 'content'], $request->search))
    ->orderBy($request->sort ?? 'published_at', 'desc')
    ->paginate(20);
```

### Create with Relations
```php
// Create article with tags
$article = Article::create([
    'title' => 'My Article',
    'content' => '...',
    'user_id' => auth()->id(),
]);
$article->tags()->attach([1, 2, 3]);  // Attach multiple tags

// Or in service
$article->tags()->sync($tags);  // Sync (attach/detach)
```

### Authorization Check
```php
// In controller
public function update(Article $article, ArticleRequest $request)
{
    $this->authorize('update', $article);  // Uses ArticlePolicy
    // Process update...
}

// In blade
@can('update', $article)
    <a href="{{ route('articles.edit', $article) }}">Edit</a>
@endcan

// In query
$articles = Article::whereHasMorph('subject', [], function ($query) {
    $query->where('status', 'published');
})->get();
```

### Redirect with Flash Message
```php
return redirect()->route('articles.show', $article)
    ->with('success', 'Article created successfully!');

// In blade
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
```

### JSON Response (API)
```php
return response()->json([
    'success' => true,
    'message' => 'Operation successful',
    'data' => $article,
], 200);

// With pagination
return response()->json([
    'data' => $articles->items(),
    'pagination' => [
        'total' => $articles->total(),
        'per_page' => $articles->perPage(),
        'current_page' => $articles->currentPage(),
        'last_page' => $articles->lastPage(),
    ]
]);
```

### Try-Catch Error Handling
```php
try {
    $article = Article::create($data);
    // Do something
} catch (QueryException $e) {
    return back()->with('error', 'Database error occurred');
} catch (Exception $e) {
    return back()->with('error', 'Something went wrong: ' . $e->getMessage());
}
```

### Cache Query Result
```php
$articles = Cache::remember('articles.published', 60*60, function() {
    return Article::published()
        ->with(['user', 'category'])
        ->latest('published_at')
        ->limit(100)
        ->get();
});

// Clear cache when needed
Cache::forget('articles.published');

// Or use tags
Cache::tags(['articles'])->flush();  // Clear all article caches
```

---

## 🎨 BLADE TEMPLATE SNIPPETS

### Form with CSRF
```blade
<form method="POST" action="{{ route('articles.store') }}" enctype="multipart/form-data">
    @csrf
    
    <div>
        <label for="title">Title</label>
        <input type="text" name="title" id="title" value="{{ old('title') }}">
        @error('title')
            <span class="text-red-500">{{ $message }}</span>
        @enderror
    </div>
    
    <button type="submit">Save</button>
</form>
```

### Conditional Rendering
```blade
@auth
    <!-- For authenticated users -->
    <p>Hello, {{ auth()->user()->name }}</p>
    
    @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}">Admin Panel</a>
    @endif
@else
    <!-- For guests -->
    <a href="{{ route('login') }}">Login</a>
@endauth

@guest
    <!-- For non-authenticated -->
@endguest
```

### Loop with Empty Check
```blade
@forelse($articles as $article)
    <div>{{ $article->title }}</div>
@empty
    <p>No articles found</p>
@endforelse
```

### Pagination Links
```blade
<div>
    @foreach($articles as $article)
        <!-- Article card -->
    @endforeach
</div>

{{ $articles->links() }}  <!-- Shows pagination controls -->
```

### Alpine.js Dropdown
```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Menu</button>
    
    <div x-show="open" @click.outside="open = false">
        <a href="#">Profile</a>
        <a href="#">Logout</a>
    </div>
</div>
```

### AJAX with Fetch
```blade
<button onclick="toggleLike('{{ $article->id }}')">
    ♥ <span id="like-count">{{ $article->likes()->count() }}</span>
</button>

<script>
function toggleLike(articleId) {
    fetch(`/articles/${articleId}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('like-count').textContent = data.count;
    })
    .catch(e => console.error('Error:', e));
}
</script>
```

### Include Component
```blade
@include('components.navbar')
@include('components.footer')

<!-- With data -->
@include('components.article-card', ['article' => $article])
```

---

## 🧪 TESTING PATTERNS

### Test Model Relationship
```php
public function test_article_belongs_to_user()
{
    $article = Article::factory()->create();
    
    $this->assertInstanceOf(User::class, $article->user);
    $this->assertEquals($article->user->id, $article->user_id);
}
```

### Test API Endpoint
```php
public function test_get_articles_API()
{
    $articles = Article::factory(3)->create();
    
    $response = $this->getJson('/api/articles');
    
    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
}
```

### Test Authorization
```php
public function test_user_cannot_edit_others_article()
{
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $article = Article::factory()->for($user1)->create();
    
    $this->actingAs($user2)
        ->put("/articles/{$article->id}", [])
        ->assertForbidden();
}
```

### Test Form Validation
```php
public function test_article_title_required()
{
    $response = $this->post('/articles', [
        'content' => 'Lorem ipsum...'
    ]);
    
    $response->assertSessionHasErrors('title');
}
```

---

## 🔍 DEBUGGING TIPS

### Use dd() to Debug
```php
dd($variable);           // Dump & die (stop execution)
dump($variable);         // Dump (continue execution)

// In blade
<pre>{{ dump($variable) }}</pre>
```

### Log Messages
```php
Log::info('Article created', ['id' => $article->id]);
Log::error('Error message', ['exception' => $e]);
Log::debug('Debug info', $data);

// Check logs
tail -f storage/logs/laravel.log
```

### Use Tinker
```bash
php artisan tinker

# In tinker shell
>>> $article = Article::first();
>>> $article->user;
>>> $article->comments()->count();
>>> $article->load(['user', 'category']);
```

### Query Debugging
```php
// Enable query logging
DB::enableQueryLog();

// Do queries
$articles = Article::published()->get();

// View queries
dd(DB::getQueryLog());

// Shows ALL SQL queries executed
```

### Browser DevTools
```javascript
// Check CSRF token
document.querySelector('meta[name="csrf-token"]').content

// Check local storage
localStorage.setItem('key', 'value');
localStorage.getItem('key');

// Console methods
console.log()
console.error()
console.table()
```

---

## 🛠️ USEFUL TOOLS & COMMANDS

### Development Server
```bash
php artisan serve --host=0.0.0.0 --port=8000  # Access from other devices
```

### Database
```bash
# View all database tables
SHOW TABLES;

# Describe table structure
DESCRIBE articles;

# Show table creation SQL
SHOW CREATE TABLE articles\G

# Quick stats
SELECT COUNT(*) FROM articles WHERE status='published';
```

### File Management
```bash
# Create storage link (public/storage → storage/app/public)
php artisan storage:link

# Clear all caches
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Remove compiled classes
php artisan down  # Maintenance mode on
php artisan up    # Maintenance mode off
```

### Git Workflow
```bash
git status                   # See changes
git add .                    # Stage all changes
git commit -m "message"      # Commit
git push origin main         # Push to remote
git log --oneline            # View commit history
git diff                     # See what changed
```

---

## 📚 FILE LOCATIONS QUICK REFERENCE

```
Models:              app/Models/
Controllers:         app/Http/Controllers/
Requests:            app/Http/Requests/
Middleware:          app/Http/Middleware/
Policies:            app/Policies/
Services:            app/Services/
Commands:            app/Console/Commands/
Routes:              routes/web.php
Views:               resources/views/
Migrations:          database/migrations/
Seeders:             database/seeders/
Tests:               tests/
Config:              config/
Environment:         .env (copy from .env.example)
CSS:                 resources/css/
JavaScript:          resources/js/
```

---

## 🎯 COMMON WORKFLOW SCENARIOS

### Workflow 1: Create New Article Feature
```bash
# 1. Create migration for new table/columns
php artisan make:migration add_featured_to_articles_table

# 2. Update model with new relationships
# Edit: app/Models/Article.php

# 3. Update controller
# Edit: app/Http/Controllers/ArticleController.php

# 4. Run migration
php artisan migrate

# 5. Test in browser
```

### Workflow 2: Fix a Bug
```bash
# 1. Identify the issue (logs, browser DevTools)
tail -f storage/logs/laravel.log

# 2. Add some debug code
Log::debug('Debug message', ['variable' => $var]);
dd($variable);

# 3. Test changes
php artisan serve

# 4. Clean up debug code
# 5. Commit changes
git add . && git commit -m "Fix: issue description"
```

### Workflow 3: Add New Form Validation
```bash
# 1. Create request class
php artisan make:request UpdateArticleRequest

# 2. Define validation rules
# Edit: app/Http/Requests/UpdateArticleRequest.php

# 3. Use in controller
public function update(Article $article, UpdateArticleRequest $request)
{
    // $request is already validated
}

# 4. Test form submission with invalid data
```

### Workflow 4: Deploy to Production
```bash
# 1. Optimize code
php artisan optimize
php artisan config:cache

# 2. Run migrations
php artisan migrate --force

# 3. Build front-end assets
npm run build

# 4. Clear caches
php artisan cache:clear

# 5. Check logs
tail -f storage/logs/laravel.log
```

---

## 🚨 TROUBLESHOOTING QUICK FIX

| Problem | Solution |
|---------|----------|
| **500 error** | Check logs: `tail -f storage/logs/laravel.log` |
| **Blank page** | Turn on debug: Set `APP_DEBUG=true` in .env |
| **Database error** | Check connection in .env, verify table exists |
| **Routes not found** | Run `php artisan route:clear` or `php artisan route:cache` |
| **Stylesheet not loading** | Run `php artisan storage:link` and `npm run build` |
| **CSRF token mismatch** | Include `@csrf` in form, check meta tag in HTML |
| **Migration error** | Check syntax in migration file, or rollback: `php artisan migrate:rollback` |
| **Seeder not working** | Run `php artisan migrate:refresh --seed` to reset DB |
| **Slow queries** | Add indexes, use eager loading `.with()`, cache queries |
| **File upload problems** | Check permissions: `chmod -R 755 storage/` |

---

## 📖 KEYBOARD SHORTCUTS (VS Code)

```
Ctrl+P           Find file
Ctrl+F           Find in file
Ctrl+H           Find & replace
Ctrl+G           Go to line
Ctrl+/           Comment/uncomment
Ctrl+D           Select word
Ctrl+Shift+L     Select all occurrences
Alt+Up/Down      Move line up/down
Ctrl+K Ctrl+R    Open keyboard shortcuts
```

---

## 🌐 USEFUL RESOURCE LINKS

**Official Docs:**
- Laravel: https://laravel.com/docs/11.x
- Blade: https://laravel.com/docs/11.x/blade
- Eloquent ORM: https://laravel.com/docs/11.x/eloquent
- Validation: https://laravel.com/docs/11.x/validation
- Authorization: https://laravel.com/docs/11.x/authorization

**Frontend Libraries:**
- Tailwind CSS Docs: https://tailwindcss.com/docs
- Alpine.js Docs: https://alpinejs.dev/
- TinyMCE Docs: https://www.tiny.cloud/docs/tinymce/6/
- MDN Web Docs: https://developer.mozilla.org/

**Helpful Tools:**
- PHP Manual: https://www.php.net/manual/
- MySQL 8.0: https://dev.mysql.com/doc/refman/8.0/en/
- Composer: https://getcomposer.org/
- NPM: https://www.npmjs.com/

---

## 💡 BEST PRACTICES CHECKLIST

- [ ] Always use migrations for database changes
- [ ] Validate ALL user input with Form Requests
- [ ] Use Policies for authorization checks
- [ ] Use eager loading to prevent N+1 queries
- [ ] Cache expensive queries with Cache::remember()
- [ ] Add indexes to frequently queried columns
- [ ] Use services for complex business logic
- [ ] Keep controllers thin and focused
- [ ] Test critical functionality
- [ ] Log important events
- [ ] Use meaningful commit messages
- [ ] Document complex code with comments
- [ ] Follow PSR-12 coding standards
- [ ] Use type hints in class properties & methods
- [ ] Handle errors gracefully with try-catch

---

**Quick Reference Version:** 2.0
**Last Updated:** February 11, 2026
**Status:** Always updated with latest best practices
