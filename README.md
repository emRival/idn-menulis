# IDN Menulis - Platform Literasi Digital untuk Siswa Indonesia

Platform literasi digital yang memungkinkan siswa Indonesia untuk berbagi artikel, mendapatkan feedback dari guru, dan belajar bersama dalam komunitas yang supportif.

## 🎯 Fitur Utama

### 1. **Authentication & Roles** 
- 3 Role: Admin, Guru, Siswa
- Email verification
- Password reset dengan aman
- Profile management dengan avatar, bio, nama lengkap, sekolah, kelas

### 2. **Artikel System**
- CRUD artikel dengan status: draft, pending, published, rejected
- 4 Kategori: Tips, Tutorial, Opini, Edukasi
- Tags (max 5 per artikel)
- Featured image upload dengan auto-resize
- TinyMCE WYSIWYG editor dengan:
  - Image upload inline
  - Code syntax highlighting
  - Autosave setiap 30 detik
  - Word counter
- Scheduled publishing
- Reading time calculation otomatis
- Views counter
- SEO-friendly slugs

### 3. **Approval Workflow**
- Siswa submit artikel → status "pending"
- Guru/Admin review → approve/reject dengan notes
- Jika approved → published
- Jika rejected → back to draft untuk edit ulang
- Guru/Admin bisa publish langsung
- History approval tracking lengkap

### 4. **Interaksi**
- Comment system dengan nested replies (max depth 2)
- Comment moderation (auto-approve untuk guru/admin)
- Like system (1 user = 1 like per artikel)
- Bookmark system
- Real-time notification:
  - Artikel approved/rejected
  - Comment baru di artikel sendiri
  - Reply comment

### 5. **Search & Filter**
- MySQL FULLTEXT search di title, content, excerpt
- Filter: kategori (multi), author, date range, status
- Sort: terbaru, terlama, most viewed, most liked
- Pagination 20 items

### 6. **Dashboard**
**Siswa:**
- Total artikel by status
- Views, likes, comments statistics
- Chart views 30 hari terakhir
- List artikel sendiri

**Guru:**
- Pending approvals (urgent notification)
- Total artikel by status
- Top 10 penulis aktif
- Approval history

**Admin:**
- Full analytics
- User management (CRUD, activate/deactivate)
- Category & Tag management
- System settings
- Activity logs

### 7. **Scheduled Tasks**
- Publish scheduled articles - every 5 minutes
- Calculate reading time - daily 02:00 WIB
- Clean soft-deleted records >30 days - Sunday 03:00 WIB
- Generate daily analytics - daily 01:00 WIB
- Send weekly digest - Monday 08:00 WIB
- Clean unverified users >7 days - daily 04:00 WIB
- Update trending cache - every 30 minutes
- Reminder pending approvals to guru - daily 09:00 WIB
- Update tag usage count - daily 03:30 WIB

## 📚 Tech Stack

- **Backend:** Laravel 11.x
- **Database:** MySQL 8.0
- **Frontend:** TailwindCSS 3.x + Alpine.js
- **Editor:** TinyMCE 6.x (WYSIWYG)
- **Package Manager:** Composer + NPM
- **PHP Version:** 8.2+

## 🚀 Installation & Setup

### Prerequisites
- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js & NPM
- Git

### Step 1: Clone Repository & Install Dependencies

```bash
# Clone repository
git clone <repository-url> idn-menulis
cd idn-menulis

# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### Step 2: Setup Environment

```bash
# Copy .env.example to .env
cp .env.example .env

# Generate app key
php artisan key:generate

# Update .env dengan konfigurasi database Anda
# Pastikan DB_DATABASE, DB_USERNAME, DB_PASSWORD sesuai dengan MySQL setup
```

### Step 3: Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE idn_menulis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed database dengan dummy data
php artisan db:seed
```

### Step 4: Build Assets

```bash
# Development mode
npm run dev

# Production build
npm run build
```

### Step 5: Setup File Storage

```bash
# Create symbolic link untuk public storage
php artisan storage:link
```

### Step 6: Start Development Server

```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Start Vite dev server
npm run dev

# Terminal 3: (Optional) Start schedule worker
php artisan schedule:work
```

## 📝 Default Credentials

Setelah seeding, gunakan credentials berikut untuk login:

### Admin Account
- **Username:** admin
- **Email:** admin1@menulis.id
- **Password:** password

### Guru Account
- Lihat database seeders untuk akun guru sample

### Siswa Account
- Lihat database seeders untuk akun siswa sample

**⚠️ Security Warning:** Jangan gunakan default password ini di production! Ubah password segera atau generate password baru.

## 📁 Project Structure

```
idn-menulis/
├── app/
│   ├── Console/
│   │   ├── Commands/          # 9 scheduled commands
│   │   └── Kernel.php          # Schedule configuration
│   ├── Events/                 # Background events
│   ├── Http/
│   │   ├── Controllers/        # Controller classes
│   │   ├── Middleware/         # Custom middleware
│   │   └── Requests/           # Form validation requests
│   ├── Listeners/              # Event listeners
│   ├── Models/                 # Eloquent models (11 models)
│   ├── Policies/               # Authorization policies
│   └── Services/               # Business logic services
├── database/
│   ├── factories/              # Model factories for seeding
│   ├── migrations/             # 11 table migrations
│   └── seeders/                # Database seeders
├── resources/
│   ├── css/
│   │   └── app.css             # Tailwind CSS
│   ├── js/
│   │   ├── app.js              # Main app JS
│   │   └── bootstrap.js        # Alpine.js & Axios setup
│   └── views/                  # Blade templates
│       ├── articles/           # Article-related views
│       ├── auth/               # Auth views
│       ├── components/         # Reusable components
│       ├── dashboard/          # Dashboard views
│       ├── layouts/            # Layout templates
│       └── admin/              # Admin panel views
├── routes/
│   ├── web.php                 # Web routes (organized & grouped)
│   └── api.php                 # API routes (optional)
├── .env.example                # Environment configuration template
├── tailwind.config.js          # Tailwind CSS configuration
├── vite.config.js              # Vite bundler configuration
├── composer.json               # PHP dependencies
└── package.json                # Node dependencies
```

## 🗄️ Database Schema (11 Tables)

1. **users** - User accounts dengan role (admin, guru, siswa)
2. **categories** - Article categories (Tips, Tutorial, Opini, Edukasi)
3. **tags** - Article tags dengan usage count
4. **articles** - Article content dengan status workflow
5. **article_tag** - Many-to-many relationship
6. **article_approvals** - Approval history tracking
7. **comments** - Nested comments dengan approval status
8. **likes** - Article likes (1 user = 1 like per artikel)
9. **bookmarks** - Article bookmarks
10. **notifications** - User notifications (approved, rejected, comments)
11. **activity_logs** - User activity tracking untuk audit

## 🔒 Security Features

- ✅ CSRF protection (enabled by default)
- ✅ XSS prevention (escape output dengan Blade)
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ Rate limiting (throttle middleware)
- ✅ Role-based access control (policies + middleware)
- ✅ File upload validation (MIME type check)
- ✅ Password hashing (bcrypt)
- ✅ Soft deletes audit trail

## ⚡ Performance Optimizations

- 🔄 Eager loading (prevent N+1 queries)
- 💾 Cache trending articles (30 min)
- 💾 Cache categories (forever)
- 🖼️ Image optimization (WebP, auto-resize)
- 📄 Pagination (20 items per page)
- 🗂️ Database indexing (proper indexes on foreign keys & commonly filtered columns)
- 📊 Fulltext search (MySQL FULLTEXT indexes)

## 🛠️ Development Tips

### Creating New Features

1. **Model:** Create model di `app/Models/`
2. **Migration:** Create migration di `database/migrations/`
3. **Controller:** Create controller di `app/Http/Controllers/`
4. **Route:** Add route di `routes/web.php`
5. **View:** Create view di `resources/views/`
6. **Request:** Create Form Request di `app/Http/Requests/`
7. **Policy:** Create policy di `app/Policies/` (if authorization needed)

### Running Commands

```bash
# Run specific migration
php artisan migrate

# Run seeders
php artisan db:seed --class=DatabaseSeeder

# Run scheduled tasks manually
php artisan articles:publish-scheduled
php artisan articles:calculate-reading-time

# Generate new model with all related files
php artisan make:model Article -mfcp

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 🧪 Testing & Quality

```bash
# Run tests
php artisan test

# Run Pint (Code formatter)
./vendor/bin/pint

# Run Pint on specific file
./vendor/bin/pint app/Models/Article.php
```

## 📖 API Documentation

Endpoints key untuk API integration:

```
GET     /api/trending-articles      - Get trending articles
GET     /api/likes/{article}        - Get like count
POST    /articles/{article}/like    - Toggle like
POST    /articles/{article}/bookmark - Toggle bookmark
```

## 🤝 Contributing

1. Create feature branch (`git checkout -b feature/amazing-feature`)
2. Commit changes (`git commit -m 'Add amazing feature'`)
3. Push to branch (`git push origin feature/amazing-feature`)
4. Open Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 📞 Support

Untuk pertanyaan atau issue, silakan buat issue di repository atau hubungi tim development.

## 🎓 Production Checklist

Sebelum deploy ke production:

- [ ] Update `.env` dengan production values
- [ ] Set `APP_DEBUG=false` di .env
- [ ] Run `composer install --no-dev`
- [ ] Run database migrations on production DB
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Optimize autoloader: `composer install --optimize-autoloader`
- [ ] Setup properly backups untuk database
- [ ] Setup email service (update MAIL_MAILER di .env)
- [ ] Setup TinyMCE API key (update TINYMCE_API_KEY)
- [ ] Configure file storage (S3, Local, dll)
- [ ] Setup cron job untuk scheduler:
  ```bash
  * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
  ```

---

**Made with ❤️ for Indonesian Digital Literacy**

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
