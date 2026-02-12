# 🔐 Security Implementation Checklist - IDN Menulis

## ✅ ENKRIPSI LOGIN & REGISTER

### Password Security
- [x] `Hash::make()` menggunakan bcrypt (otomatis via `'password' => 'hashed'` cast)
- [x] Tidak pernah menyimpan password plaintext
- [x] Verifikasi dengan `Hash::check()` (via Auth::attempt)
- [x] Password minimum 8 karakter
- [x] Kombinasi huruf besar, kecil, angka & simbol (StrongPassword rule)
- [x] Check common passwords
- [x] Optional: Check Have I Been Pwned API

### Enkripsi Data Sensitif
- [x] Phone terenkripsi via `EncryptsAttributes` trait
- [x] Two Factor Secret terenkripsi
- [x] Recovery Codes terenkripsi
- [x] Menggunakan `Crypt::encryptString()` / `decryptString()`

### HTTPS Only
- [x] Middleware `ForceHttps` redirect HTTP → HTTPS di production
- [x] Session secure cookie (`SESSION_SECURE_COOKIE=true`)
- [x] HttpOnly cookie (`SESSION_HTTP_ONLY=true`)
- [x] SameSite=strict (`SESSION_SAME_SITE=strict`)

### CSRF Protection
- [x] `@csrf` di semua form Blade
- [x] Token validasi di backend (Laravel default)
- [x] Modal login menggunakan CSRF token dari meta tag
- [x] Laravel Sanctum tersedia untuk API protection

---

## ✅ ENKRIPSI ARTIKEL

### Enkripsi Konten Artikel
- [x] Method `encryptContent()` untuk artikel private/premium
- [x] Method `getDecryptedContentAttribute()` untuk dekripsi
- [x] Flag `is_encrypted` untuk tracking status enkripsi

### Enkripsi ID Artikel (Anti IDOR)
- [x] Package `hashids/hashids` terinstall
- [x] `EncryptionService::encodeId()` / `decodeId()`
- [x] Model attribute `$article->hash_id` untuk URL
- [x] URL: `/artikel/{slug}` (menggunakan slug, bukan ID)

### Protect API Response
- [x] `ArticleResource` - tidak expose `user_id`
- [x] `UserResource` - filter field sensitif
- [x] Hidden fields di model:
  - User: `password`, `remember_token`, `two_factor_secret`, `two_factor_recovery_codes`, `phone`, `last_login_ip`
  - Article: `user_id`

---

## ✅ SESSION ENCRYPTION

- [x] `SESSION_DRIVER=database` (secure storage)
- [x] `SESSION_ENCRYPT=true` dalam config (optional, aktifkan via .env)
- [x] Cookie secure & HttpOnly
- [x] Session regenerate setelah login (`$request->session()->regenerate()`)
- [x] CSRF token rotate setelah login (`$request->session()->regenerateToken()`)
- [x] Session timeout configurable (default 120 menit)

---

## ✅ DATABASE SECURITY

- [x] Gunakan user MySQL khusus (bukan root) - configure di .env production
- [x] APP_KEY aman & unique
- [x] File `.env` di luar public directory
- [x] `.env` blocked di `.htaccess`
- [x] Eloquent ORM (prevent SQL injection)
- [x] Tidak menggunakan raw SQL queries

---

## ✅ ADVANCED PROTECTION

### Two Factor Authentication (2FA)
- [x] Trait `HasTwoFactorAuthentication`
- [x] `TwoFactorController` untuk setup
- [x] Recovery codes terenkripsi
- [x] Configurable di `config/security.php`

### Login Rate Limit
- [x] 5x gagal = lockout 1 menit (configurable)
- [x] Progressive delay
- [x] Account locking setelah 10 gagal
- [x] IP-based throttling

### Activity Log Login
- [x] `LoginAttempt` model - log semua percobaan login
- [x] `ActivityLog` model - log aktivitas user
- [x] Log IP address & User Agent
- [x] Log successful & failed attempts

### Device Detection
- [x] User Agent logging
- [x] IP address tracking
- [x] `last_login_ip` di tabel users

### Encrypted Remember Token
- [x] `setRememberTokenAttribute()` menggunakan HMAC

### Signed URL untuk Artikel Private
- [x] `$article->getSignedUrl()` - URL dengan tanda tangan
- [x] Expiry time configurable
- [x] Validasi signature di middleware

### Middleware Decrypt Otomatis
- [x] `DecryptRouteParameters` middleware
- [x] `ArticleAccessControl` middleware

---

## ✅ FILE UPLOAD ENCRYPTION

- [x] Filename random via `ImageService`
- [x] Simpan di `storage/app/public`
- [x] Validasi MIME type & extension
- [x] `FileUploadSecurity` middleware
- [x] Max size configurable (10MB)
- [x] Blocked extensions list
- [x] Optional file encryption via config

---

## ✅ SECURITY MIDDLEWARE AKTIF

1. **ForceHttps** - Redirect ke HTTPS di production
2. **SecurityHeaders** - X-Frame-Options, CSP, HSTS, dll
3. **XSSProtection** - Filter input XSS
4. **SQLInjectionProtection** - Detect SQL injection patterns
5. **CommandInjectionProtection** - Block command injection
6. **SessionSecurity** - Session hijacking protection
7. **ArticleAccessControl** - Protect private/premium content
8. **FileUploadSecurity** - Secure file uploads
9. **IDORProtection** - Prevent IDOR attacks

---

## ✅ CONFIG FILES

### config/security.php
```php
'rate_limits' => [
    'login' => ['max_attempts' => 5, 'decay_minutes' => 1],
    // ...
],
'password' => [
    'min_length' => 8,
    'require_uppercase' => true,
    'require_lowercase' => true,
    'require_number' => true,
    'require_special' => true,
],
'brute_force' => [
    'enabled' => true,
    'max_attempts' => 5,
    'lockout_time' => 60,
    'block_ip_after' => 10,
],
```

### config/session.php
```php
'driver' => 'database',
'lifetime' => 120,
'encrypt' => true,  // Enable via .env
'secure' => true,
'http_only' => true,
'same_site' => 'strict',
```

---

## 🚀 CHECKLIST SEBELUM PRODUCTION

### Wajib
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL=https://yourdomain.com`
- [ ] HTTPS aktif dengan SSL certificate
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] Database user khusus (bukan root)
- [ ] APP_KEY unique untuk production
- [ ] File `.env` tidak accessible dari web

### Recommended
- [ ] Enable `SESSION_ENCRYPT=true`
- [ ] Setup 2FA untuk admin/guru
- [ ] Aktifkan HSTS (`HSTS_ENABLED=true`)
- [ ] Backup database terenkripsi
- [ ] Setup monitoring & alerting
- [ ] Review error logs

---

## 🛡️ PROTEKSI TERHADAP SERANGAN

| Attack Type | Protection |
|-------------|------------|
| SQL Injection | ✅ Eloquent ORM, SQLInjectionProtection middleware |
| XSS | ✅ Blade escaping, XSSProtection middleware |
| CSRF | ✅ @csrf token, SameSite cookies |
| IDOR | ✅ Hashid URLs, authorization checks, IDORProtection |
| Brute Force | ✅ Rate limiting, account lockout |
| Session Hijacking | ✅ Secure cookies, session regeneration, HttpOnly |
| Data Leak | ✅ Field encryption, hidden attributes, API Resources |
| Man in the Middle | ✅ Force HTTPS, HSTS, secure cookies |
| Database Breach | ✅ Encrypted sensitive fields, hashed passwords |

---

## 📁 FILE STRUCTURE

```
app/
├── Http/
│   ├── Middleware/
│   │   ├── ForceHttps.php
│   │   ├── SecurityHeaders.php
│   │   ├── XSSProtection.php
│   │   ├── SQLInjectionProtection.php
│   │   ├── CommandInjectionProtection.php
│   │   ├── SessionSecurity.php
│   │   ├── ArticleAccessControl.php
│   │   ├── DecryptRouteParameters.php
│   │   ├── FileUploadSecurity.php
│   │   └── IDORProtection.php
│   └── Resources/
│       ├── ArticleResource.php
│       └── UserResource.php
├── Models/
│   ├── User.php (with encryption)
│   ├── Article.php (with encryption)
│   ├── LoginAttempt.php
│   └── ActivityLog.php
├── Rules/
│   └── StrongPassword.php
├── Services/
│   └── EncryptionService.php
└── Traits/
    ├── EncryptsAttributes.php
    └── HasTwoFactorAuthentication.php

config/
├── security.php
└── session.php
```

---

## 🔧 USAGE EXAMPLES

### Encrypt/Decrypt Data
```php
use App\Services\EncryptionService;

$encryption = app(EncryptionService::class);

// Encrypt
$encrypted = $encryption->encrypt('sensitive data');

// Decrypt
$decrypted = $encryption->decrypt($encrypted);
```

### Hashid for URLs
```php
// Encode ID
$hashId = $encryption->encodeId(123); // "f83k29dj29"

// Decode ID
$id = $encryption->decodeId('f83k29dj29'); // 123
```

### Password Validation
```php
use App\Rules\StrongPassword;

$request->validate([
    'password' => ['required', 'confirmed', new StrongPassword()],
]);
```

### Signed URL for Private Content
```php
$signedUrl = $article->getSignedUrl(60); // Valid 60 minutes
```

### Article with Encryption
```php
// Auto decrypt when accessed
$content = $article->decrypted_content;

// Manual encrypt
$article->encryptContent();
```
