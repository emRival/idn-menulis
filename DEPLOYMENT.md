# Panduan Deployment IDNMenulis ke Shared Hosting

## Langkah-langkah Upload

### 1. Persiapan Lokal
```bash
# Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Struktur Folder di Hosting

**PENTING:** Di shared hosting (cPanel), struktur folder harus seperti ini:

```
/home/username/
├── public_html/          ← ISI FOLDER public/ Laravel DISINI
│   ├── index.php         ← EDIT FILE INI
│   ├── .htaccess
│   ├── favicon.ico
│   ├── robots.txt
│   ├── css/
│   ├── js/
│   └── storage/          ← Symlink ke ../laravel/storage/app/public
│
└── laravel/              ← ISI SEMUA FILE LARAVEL KECUALI public/
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    ├── .env              ← KONFIGURASI PRODUCTION
    ├── artisan
    └── composer.json
```

### 3. Edit index.php di public_html

Ubah path di `public_html/index.php`:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../laravel/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../laravel/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../laravel/bootstrap/app.php')
    ->handleRequest(Request::capture());
```

### 4. Konfigurasi .env di Hosting

Copy `.env.production` ke `.env` dan sesuaikan:

```env
APP_NAME="IDN Menulis"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://artikel.reyhan16.my.id

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=username_database
DB_PASSWORD=password_database

SESSION_DOMAIN=artikel.reyhan16.my.id
SESSION_SECURE_COOKIE=true
```

### 5. Buat Storage Link Manual

Di cPanel File Manager atau SSH:

```bash
# Hapus folder storage di public_html jika ada
rm -rf public_html/storage

# Buat symlink
ln -s ../laravel/storage/app/public public_html/storage
```

Atau manual di cPanel:
1. Buka File Manager
2. Hapus folder `public_html/storage` jika ada
3. Buat symlink dengan Terminal (jika tersedia)

### 6. Set Permissions

```bash
chmod -R 755 laravel/
chmod -R 775 laravel/storage/
chmod -R 775 laravel/bootstrap/cache/
```

### 7. Jalankan Migration (via SSH atau Artisan di cPanel)

```bash
cd ~/laravel
php artisan migrate --force
php artisan db:seed --force  # Jika perlu
```

### 8. Clear & Optimize

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link  # Jika belum
```

## Troubleshooting Error 403

### Penyebab Umum:

1. **Permissions salah**
   ```bash
   chmod -R 755 laravel/
   chmod -R 775 laravel/storage/
   chmod -R 775 laravel/bootstrap/cache/
   ```

2. **Document Root salah**
   - Pastikan domain mengarah ke `public_html/` 

3. **PHP Version**
   - Pastikan PHP 8.1+ di cPanel

4. **ModSecurity blocking**
   - Hubungi hosting untuk disable ModSecurity sementara
   - Atau whitelist domain Anda

5. **Missing .htaccess**
   - Pastikan file `.htaccess` ter-upload (file tersembunyi)

6. **Security Middleware Laravel**
   - Sudah dinonaktifkan di `bootstrap/app.php`
   - XSSProtection, SQLInjectionProtection, CommandInjectionProtection

### Cek Error Log:

Di cPanel → Error Log atau:
```bash
tail -f ~/laravel/storage/logs/laravel.log
```

## File yang Harus di-Upload

### Ke `public_html/`:
- Semua isi folder `public/` dari Laravel

### Ke folder `laravel/` (di luar public_html):
- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `lang/` (jika ada)
- `resources/`
- `routes/`
- `storage/`
- `vendor/`
- `.env` (edit untuk production)
- `artisan`
- `composer.json`
- `composer.lock`

### JANGAN upload:
- `.git/`
- `node_modules/`
- `.env.local`
- `tests/`
- `*.zip`

## Checklist Sebelum Go Live

- [ ] APP_DEBUG=false
- [ ] APP_ENV=production
- [ ] APP_URL sesuai domain
- [ ] Database credentials benar
- [ ] Storage link sudah dibuat
- [ ] Permissions 755/775
- [ ] HTTPS aktif
- [ ] Cache sudah di-generate
