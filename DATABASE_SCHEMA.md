# IDN Menulis - Database Schema & Relationships

## 🗄️ Complete Database Documentation

Dokumentasi lengkap tentang struktur database, relasi, dan indexing strategy.

---

## 📊 DATABASE OVERVIEW

**Database Name:** `idn_menulis`
**Character Set:** utf8mb4
**Collation:** utf8mb4_unicode_ci
**Total Tables:** 11
**Engine:** InnoDB (supports transactions & foreign keys)

---

## 📋 TABLE DEFINITIONS

### 1. **users** - User Accounts & Profiles

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,      -- Unique username for login
    email VARCHAR(255) UNIQUE NOT NULL,         -- Email address
    email_verified_at TIMESTAMP NULL,           -- Email verification
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) NULL,                   -- Avatar image path
    full_name VARCHAR(255) NOT NULL,            -- Full name (display)
    bio TEXT NULL,                              -- User biography
    school_name VARCHAR(255) NULL,              -- School/Institution
    class VARCHAR(50) NULL,                     -- Class (for siswa)
    role ENUM('admin','guru','siswa') INDEX,    -- User role
    is_active BOOLEAN DEFAULT TRUE INDEX,       -- Account active status
    last_login_at TIMESTAMP NULL,               -- Last login datetime
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL                   -- Soft delete
);

-- Indexes for quick lookup
KEY username_index (username)
KEY email_index (email)
KEY role_index (role)
KEY is_active_index (is_active)
```

**Roles:**
- **admin** - Full system access (3-5 expected)
- **guru** - Can approve articles, moderate comments (10-20 expected)
- **siswa** - Regular users working on articles (50+ expected)

**Relationships:**
- hasMany: articles, comments, article_approvals (as reviewer), notifications, activity_logs
- belongsToMany: articles (via likes), articles (via bookmarks)

---

### 2. **categories** - Article Categories

```sql
CREATE TABLE categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,          -- Category name
    slug VARCHAR(255) UNIQUE NOT NULL INDEX,    -- URL-friendly slug
    description TEXT NULL,                      -- Category description
    icon VARCHAR(50) NULL,                      -- Icon class (e.g., 'fa-pencil')
    color VARCHAR(7) NULL,                      -- Hex color code
    is_active BOOLEAN DEFAULT TRUE INDEX,       -- Active status
    order_position INT DEFAULT 0,                -- Sort order
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Indexes
KEY slug_index (slug)
KEY is_active_index (is_active)
```

**Default Categories:**
- Tips & Trik (Color: #FF6B6B)
- Tutorial (Color: #4ECDC4)
- Opini (Color: #FFE66D)
- Edukasi (Color: #95E1D3)

**Relationships:**
- hasMany: articles

---

### 3. **tags** - Article Tags

```sql
CREATE TABLE tags (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,          -- Tag name
    slug VARCHAR(255) UNIQUE NOT NULL INDEX,    -- URL slug
    usage_count INT DEFAULT 0 INDEX,            -- How many articles use this tag
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Indexes
KEY slug_index (slug)
KEY usage_count_index (usage_count)
```

**Relationships:**
- belongsToMany: articles (via article_tag pivot table)

**Methods:**
- `incrementUsage()` - Increment usage_count
- `decrementUsage()` - Decrement usage_count

---

### 4. **articles** - User Articles/Posts

```sql
CREATE TABLE articles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,           -- FK to users (author)
    category_id BIGINT UNSIGNED NOT NULL,       -- FK to categories
    title VARCHAR(255) UNIQUE NOT NULL,         -- Article title
    slug VARCHAR(255) UNIQUE NOT NULL INDEX,    -- URL slug
    excerpt VARCHAR(500) NULL,                  -- Short summary
    content LONGTEXT NOT NULL,                  -- Full article content
    featured_image VARCHAR(255) NULL,           -- Featured image path
    status ENUM('draft','pending','published',  -- Article status
               'scheduled','rejected') INDEX,
    rejection_reason TEXT NULL,                 -- If rejected, why?
    is_featured BOOLEAN DEFAULT FALSE INDEX,    -- Pinned article
    views_count INT DEFAULT 0 INDEX,            -- View counter
    reading_time INT NULL,                      -- Estimated reading time (mins)
    published_at TIMESTAMP NULL INDEX,          -- Publication datetime
    scheduled_at TIMESTAMP NULL INDEX,          -- Schedule publication time
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,                  -- Soft delete
    
    FULLTEXT search_index (title, content, excerpt),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

-- Indexes
KEY user_id_index (user_id)
KEY category_id_index (category_id)
KEY slug_index (slug)
KEY status_index (status)
KEY is_featured_index (is_featured)
KEY views_count_index (views_count)
KEY published_at_index (published_at)
KEY scheduled_at_index (scheduled_at)
```

**Status Flow:**
```
draft → pending → published ✓
draft → published (if admin/guru)
pending → rejected (with reason) → draft (revise) → pending
draft → scheduled (future publish) → published (at scheduled_at)
```

**Key Fields:**
- `slug` - Used for SEO friendly URLs (e.g., /articles/how-to-write-better)
- `reading_time` - Calculated on creation/update (200 words/minute)
- `views_count` - Incremented each time article is viewed
- `status` - Determines visibility & what actions are allowed

**Relationships:**
- belongsTo: user (author), category
- hasMany: comments, article_approvals
- belongsToMany: tags, users (via likes), users (via bookmarks)

---

### 5. **article_tag** - Pivot Table (Articles ↔ Tags)

```sql
CREATE TABLE article_tag (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    article_id BIGINT UNSIGNED NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP,
    
    UNIQUE KEY unique_article_tag (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    KEY tag_id_index (tag_id)
);
```

**Purpose:** Links articles to their tags (many-to-many relationship)

**Example:**
```
Article 1 ("Tips for Writing") → Tags: ['Writing', 'Education', 'Tips']
Article 2 ("Story Time") → Tags: ['Writing', 'Fiction', 'Storytelling']
```

---

### 6. **article_approvals** - Approval History

```sql
CREATE TABLE article_approvals (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    article_id BIGINT UNSIGNED NOT NULL,        -- FK to articles
    reviewer_id BIGINT UNSIGNED NOT NULL,       -- FK to users (guru/admin)
    previous_status VARCHAR(50) NOT NULL,       -- Status before review
    new_status VARCHAR(50) NOT NULL,            -- Status after review
    notes TEXT NULL,                            -- Reviewer's notes
    reviewed_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP,
    
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE RESTRICT,
    KEY article_reviewed_index (article_id, reviewed_at)
);
```

**Example Entry:**
```
article_id: 5
reviewer_id: 3 (guru account)
previous_status: "pending"
new_status: "published"
notes: "Well written, approved!"
reviewed_at: 2026-02-11 14:30:00
```

**Relationships:**
- belongsTo: article, reviewer (user)

---

### 7. **comments** - Article Comments & Replies

```sql
CREATE TABLE comments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    article_id BIGINT UNSIGNED NOT NULL,        -- FK to articles
    user_id BIGINT UNSIGNED NOT NULL,           -- FK to users (commenter)
    parent_id BIGINT UNSIGNED NULL,             -- FK to comments (for replies)
    content TEXT NOT NULL,                      -- Comment text
    is_approved BOOLEAN DEFAULT FALSE INDEX,    -- Moderation status
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,                  -- Soft delete
    
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
    KEY article_approved_index (article_id, is_approved),
    KEY parent_id_index (parent_id)
);
```

**Comment Structure:**
```
Comment 1 (parent_id: NULL)
  └── Reply to Comment 1 (parent_id: 1)
      └── Cannot reply further (max depth 2)

So:
   Comment
   └── Reply
       └── [NO MORE NESTING]
```

**Relationships:**
- belongsTo: article, user, parent (comment)
- hasMany: replies (child comments)

---

### 8. **likes** - Article Likes

```sql
CREATE TABLE likes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    article_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP,
    
    UNIQUE KEY unique_user_article (user_id, article_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    KEY article_id_index (article_id)
);
```

**Constraint:** Each user can like an article only ONCE (unique constraint)

**Relationships:**
- belongsTo: user, article

---

### 9. **bookmarks** - Article Bookmarks

```sql
CREATE TABLE bookmarks (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    article_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP,
    
    UNIQUE KEY unique_user_article (user_id, article_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    KEY article_id_index (article_id)
);
```

**Constraint:** Each user can bookmark an article only ONCE (unique constraint)

---

### 10. **notifications** - User Notifications

```sql
CREATE TABLE notifications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,           -- FK to users
    type VARCHAR(50) INDEX,                     -- Notification type
    title VARCHAR(255) NOT NULL,                -- Notification title
    message TEXT NOT NULL,                      -- Notification message
    action_url VARCHAR(500) NULL,               -- URL to click (e.g., article link)
    is_read BOOLEAN DEFAULT FALSE INDEX,        -- Read status
    created_at TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY user_read_index (user_id, is_read)
);
```

**Notification Types:**
- `article_approved` - Article approved by guru
- `article_rejected` - Article rejected with reason
- `comment_on_article` - Someone commented on user's article
- `comment_reply` - Someone replied to user's comment
- `article_mentioned` - User mentioned in comment
- `pending_approval` - Guru has articles waiting approval

**Relationships:**
- belongsTo: user

---

### 11. **activity_logs** - User Activity Tracking

```sql
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,               -- FK to users (can be NULL for guests)
    action VARCHAR(100) NOT NULL,               -- Action performed
    description TEXT NOT NULL,                  -- Action details
    subject_type VARCHAR(100) NULL,             -- What was affected (e.g., 'Article')
    subject_id BIGINT UNSIGNED NULL,            -- ID of affected record
    ip_address VARCHAR(45) NULL,                -- User's IP address
    user_agent TEXT NULL,                       -- Browser info
    created_at TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    KEY user_created_index (user_id, created_at),
    KEY action_index (action),
    KEY subject_index (subject_type, subject_id)
);
```

**Example Logs:**
```
{
    user_id: 5,
    action: "article.created",
    description: "Created new article: 'Tips for Writing'",
    subject_type: "Article",
    subject_id: 42,
    ip_address: "192.168.1.100",
    user_agent: "Mozilla/5.0..."
}

{
    user_id: 3,
    action: "article.approved",
    description: "Approved article 'Tips for Writing'",
    subject_type: "Article",
    subject_id: 42,
    ip_address: "192.168.1.50"
}

{
    user_id: 5,
    action: "article.viewed",
    description: "Viewed article: 'Tips for Writing'",
    subject_type: "Article",
    subject_id: 42,
    ip_address: "192.168.1.100"
}
```

---

## 🔗 RELATIONSHIP DIAGRAM

```
┌─────────────┐
│    users    │ (11 users: 3 admin, 10 guru, 50 siswa)
└──────┬──────┘
       │
       ├─ (1:N) ──→ articles
       │            (100 articles across 4 categories)
       │
       ├─ (1:N) ──→ comments
       │            (500 comments with replies)
       │
       ├─ (1:N) ──→ article_approvals
       │            (100+ approval records)
       │
       ├─ (1:N) ──→ notifications
       │            (200+ notifications)
       │
       ├─ (1:N) ──→ activity_logs
       │            (1000+ activity entries)
       │
       ├─ (N:N) ──→ articles (via likes)
       │            (1000 likes)
       │
       └─ (N:N) ──→ articles (via bookmarks)
                    (300 bookmarks)

┌──────────────┐
│  categories  │ (4 categories)
└──────┬───────┘
       │
       └─ (1:N) ──→ articles


┌──────────┐
│   tags   │ (20 tags)
└────┬─────┘
     │
     └─ (N:N) ──→ articles (via article_tag)
                  (200+ article-tag relationships)


┌─────────────┐
│  articles   │ (100 articles)
└────┬────────┘
     │
     ├─ (N:1) ──→ users (author)
     ├─ (N:1) ──→ categories
     ├─ (N:N) ──→ tags
     ├─ (1:N) ──→ comments
     ├─ (1:N) ──→ article_approvals
     ├─ (N:N) ──→ users (via likes)
     └─ (N:N) ──→ users (via bookmarks)

┌──────────┐
│ comments │ (500 comments + replies)
└────┬─────┘
     │
     ├─ (N:1) ──→ articles
     ├─ (N:1) ──→ users (commenter)
     └─ (N:1) ──→ comments (parent - for nested replies)
```

---

## 📈 INDEXING STRATEGY

### Purpose of Indexes
- **Speed up queries** - Find records faster
- **Enforce uniqueness** - Prevent duplicates
- **Support sorting** - ORDER BY performance
- **Support filtering** - WHERE clause performance

### Index Types Used

**1. Single Column Indexes (Most Common)**
```sql
KEY user_id_index (user_id)      -- Fast lookups by user
KEY status_index (status)         -- Filter by status
KEY is_active_index (is_active)   -- Find active records
```

**2. Composite Indexes (Multiple Columns)**
```sql
KEY article_approved_index (article_id, is_approved)
-- Optimizes queries like: WHERE article_id = ? AND is_approved = ?
-- Or just: WHERE article_id = ? (can use first part)

KEY user_read_index (user_id, is_read)
-- Optimizes: WHERE user_id = ? AND is_read = ?
```

**3. Unique Indexes (Prevent Duplicates)**
```sql
UNIQUE KEY unique_article_tag (article_id, tag_id)
-- Prevents: same article tagged with same tag twice

UNIQUE KEY unique_user_article (user_id, article_id)
-- Prevents: same user liking/bookmarking same article twice
```

**4. Full Text Indexes (Search)**
```sql
FULLTEXT search_index (title, content, excerpt)
-- Enables: NATURAL LANGUAGE search in article text
-- Query: WHERE MATCH(title, content) AGAINST('keyword' IN BOOLEAN MODE)
```

---

## 💾 SEEDING DATA

### Current Seed Data

**Users (63 total):**
```
- 3 Admin users (admin1, admin2, admin3)
- 10 Guru users (generated via factory)
- 50 Siswa users (generated via factory)
```

**Articles (100 total):**
```
- Status Distribution:
  ├── ~40 Published
  ├── ~30 Pending (awaiting approval)
  ├── ~20 Draft (not submitted)
  ├── ~8 Scheduled (will publish in future)
  └── ~2 Rejected (sent back to author)
```

**Comments (500 total):**
```
- Mix of top-level comments and replies
- Max nesting depth: 2 (comment → reply)
- Variable approval status (some approved, some pending)
```

**Likes (1000 total):**
```
- Distributed randomly across articles
- One-per-user per-article (enforced by unique constraint)
```

**Bookmarks (300 total):**
```
- Distributed randomly across articles
- One-per-user per-article (enforced by unique constraint)
```

---

## 🔍 EFFICIENT QUERIES

### Get User's Dashboard Stats
```php
$user = auth()->user();
$stats = [
    'articles' => $user->articles()->count(),
    'total_views' => $user->articles()->sum('views_count'),
    'total_likes' => Like::whereIn(
        'article_id',
        $user->articles()->pluck('id')
    )->count(),
    'comments' => $user->comments()->count(),
];
```

### Get Articles with All Related Data
```php
$articles = Article::published()
    ->with(['user', 'category', 'tags', 'comments'])
    ->inRandomOrder()
    ->limit(10)
    ->get();
```

### Search Articles
```php
Article::published()
    ->whereFullText(['title', 'content', 'excerpt'], $keyword)
    ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
    ->when($tagId, fn($q) => $q->whereHas('tags', fn($q2) => $q2->where('id', $tagId)))
    ->paginate(20);
```

### Get Pending Approvals for Guru
```php
$pending = Article::where('status', 'pending')
    ->with(['user', 'category', 'comments'])
    ->orderBy('created_at')
    ->paginate(20);
```

### Get Unread Notifications
```php
$notifications = auth()->user()
    ->notifications()
    ->where('is_read', false)
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();
```

---

## 🔐 DATA INTEGRITY CONSTRAINTS

### Foreign Key Constraints
```sql
-- Deleting user deletes their articles
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

-- Deleting article keeps approval records (soft delete)
FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE

-- Cannot delete category that has articles
FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
```

### Unique Constraints
```sql
-- Each username must be unique
UNIQUE KEY username_index (username)

-- User can only like/bookmark article once
UNIQUE KEY unique_user_article (user_id, article_id)

-- Each tag assigned once per article
UNIQUE KEY unique_article_tag (article_id, tag_id)
```

### Data Validation
- Username: alpha_dash (allow letters, numbers, dashes)
- Email: valid email format
- Role: must be 'admin', 'guru', or 'siswa'
- Status: must be in defined list
- Reading time: >= 1 minute

---

## 📊 QUERY PERFORMANCE TIPS

1. **Use eager loading** to prevent N+1 queries:
   ```php
   Article::with(['user', 'category', 'tags'])->get();
   ```

2. **Use indexes** for WHERE, ORDER BY, JOIN conditions:
   ```php
   -- Creates index on frequently filtered columns
   KEY status_index (status)
   ```

3. **Use FULLTEXT search** for text search:
   ```php
   Article::whereFullText(['title', 'content'], $keyword)
   ```

4. **Cache frequent queries**:
   ```php
   Cache::remember('trending_articles', 30*60, function() {
       return Article::published()
           ->orderByDesc('views_count')
           ->limit(5)
           ->get();
   });
   ```

5. **Use pagination** to limit results:
   ```php
   Article::paginate(20);  // Returns 20 per page
   ```

---

**Database Version:** MySQL 8.0+
**Character Set:** utf8mb4 (supports emoji & special chars)
**Last Updated:** February 11, 2026
