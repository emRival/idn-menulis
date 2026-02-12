# 🔒 Security Implementation Guide - IDN Menulis

## Overview

Sistem keamanan IDN Menulis telah diimplementasikan dengan standar industri untuk melindungi dari berbagai jenis serangan.

---

## 📋 Checklist Keamanan

### ✅ Backend Security

| Fitur | Status | File |
|-------|--------|------|
| SQL Injection Protection | ✅ | `app/Http/Middleware/SQLInjectionProtection.php` |
| XSS Protection | ✅ | `app/Http/Middleware/XSSProtection.php` |
| CSRF Protection | ✅ | Laravel built-in |
| Mass Assignment Protection | ✅ | Model `$fillable` |
| Brute Force Protection | ✅ | `app/Http/Middleware/ThrottleLogin.php` |
| Session Hijacking Protection | ✅ | `app/Http/Middleware/SessionSecurity.php` |
| IDOR Protection | ✅ | `app/Http/Middleware/IDORProtection.php` |
| File Upload Security | ✅ | `app/Http/Middleware/FileUploadSecurity.php` |
| Command Injection Protection | ✅ | `app/Http/Middleware/CommandInjectionProtection.php` |
| SSRF Protection | ✅ | `app/Http/Middleware/CommandInjectionProtection.php` |
| Deserialization Attack Protection | ✅ | `app/Http/Middleware/CommandInjectionProtection.php` |
| Security Headers | ✅ | `app/Http/Middleware/SecurityHeaders.php` |

### ✅ Frontend Security

| Fitur | Status | Implementation |
|-------|--------|----------------|
| CSRF Meta Token | ✅ | `resources/views/layouts/app.blade.php` |
| AJAX CSRF Setup | ✅ | JavaScript in layout |
| Frame Busting | ✅ | JavaScript protection |
| CSP Headers | ✅ | SecurityHeaders middleware |
| Clickjacking Protection | ✅ | X-Frame-Options header |

### ✅ Authentication & Authorization

| Fitur | Status | Implementation |
|-------|--------|----------------|
| Two-Factor Authentication | ✅ | `app/Traits/HasTwoFactorAuthentication.php` |
| Login Rate Limiting | ✅ | `ThrottleLogin` middleware |
| Session Security | ✅ | `SessionSecurity` middleware |
| Logout All Devices | ✅ | `SecurityService::invalidateAllSessions()` |
| Password Strength Validation | ✅ | `SecurityService::validatePasswordStrength()` |

---

## 🛠️ Installation

### 1. Install Dependencies

```bash
composer require pragmarx/google2fa-laravel
composer require laravel/sanctum
```

### 2. Run Migrations

```bash
php artisan migrate
```

### 3. Publish Config

```bash
php artisan vendor:publish --tag=sanctum-config
```

### 4. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## 🔐 Configuration

### Environment Variables

Add to your `.env`:

```env
# Security Settings
SECURITY_ADMIN_EMAIL=admin@idnmenulis.com
SECURITY_2FA_ENABLED=true
SECURITY_EMAIL_VERIFICATION=true
SECURITY_IP_BLOCKING=true
SECURITY_ACTIVITY_LOGGING=true
SECURITY_SUSPICIOUS_DETECTION=true

# Admin IP Whitelist (optional)
ADMIN_IP_WHITELIST=

# Session Security
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
```

### Production Settings

For production, ensure:

```env
APP_ENV=production
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
```

---

## 📁 File Structure

```
app/
├── Http/
│   ├── Middleware/
│   │   ├── SecurityHeaders.php          # Security headers (CSP, HSTS, etc)
│   │   ├── XSSProtection.php             # XSS sanitization
│   │   ├── SQLInjectionProtection.php    # SQL injection detection
│   │   ├── CommandInjectionProtection.php # Command/SSRF/Deserialization
│   │   ├── SessionSecurity.php           # Session hijacking protection
│   │   ├── ThrottleLogin.php             # Brute force protection
│   │   ├── IDORProtection.php            # IDOR protection
│   │   ├── FileUploadSecurity.php        # File upload validation
│   │   └── AdminIPRestriction.php        # Admin IP whitelist
│   ├── Controllers/
│   │   └── Auth/
│   │       └── TwoFactorController.php   # 2FA management
│   └── Requests/
│       ├── SecureLoginRequest.php        # Login validation
│       ├── SecureRegisterRequest.php     # Registration validation
│       └── SecureFileUploadRequest.php   # File upload validation
├── Models/
│   ├── SecurityLog.php                   # Security event logging
│   └── LoginAttempt.php                  # Login attempts tracking
├── Services/
│   └── SecurityService.php               # Security utilities
├── Traits/
│   ├── EncryptsAttributes.php            # Model attribute encryption
│   └── HasTwoFactorAuthentication.php    # 2FA trait for User
└── Notifications/
    ├── BruteForceAttempt.php             # Brute force alert
    ├── SuspiciousActivityDetected.php    # Suspicious activity alert
    └── NewDeviceLogin.php                # New device login alert

config/
└── security.php                          # Security configuration

database/migrations/
├── 2026_02_12_100001_add_two_factor_columns_to_users_table.php
├── 2026_02_12_100002_create_security_logs_table.php
└── 2026_02_12_100003_create_login_attempts_table.php
```

---

## 🔧 Usage Examples

### 1. Enable 2FA for User

```php
$user = Auth::user();
$setup = $user->enableTwoFactorAuth();

// Display QR code
echo $setup['qr_code_url'];

// Show recovery codes
print_r($setup['recovery_codes']);
```

### 2. Validate 2FA Code

```php
if ($user->validateTwoFactorCode($code)) {
    // Valid
} else {
    // Invalid
}
```

### 3. Encrypt Sensitive Data

```php
use App\Services\SecurityService;

$security = app(SecurityService::class);

// Encrypt
$encrypted = $security->encrypt('sensitive data');

// Decrypt
$decrypted = $security->decrypt($encrypted);
```

### 4. Log Security Event

```php
use App\Models\SecurityLog;

SecurityLog::log(
    SecurityLog::EVENT_SUSPICIOUS_ACTIVITY,
    $userId,
    $request->ip(),
    $request->userAgent(),
    ['details' => 'description'],
    'warning'
);
```

### 5. Block IP

```php
use App\Services\SecurityService;

$security = app(SecurityService::class);
$security->blockIP('1.2.3.4', 60, 'Brute force attempt');
```

---

## 🛡️ Middleware Registration

All security middlewares are registered in `bootstrap/app.php`:

```php
// Global middleware
$middleware->appendToGroup('web', [
    SecurityHeaders::class,
    XSSProtection::class,
    SQLInjectionProtection::class,
    CommandInjectionProtection::class,
]);

// Route middleware aliases
$middleware->alias([
    'throttle.login' => ThrottleLogin::class,
    'idor.protection' => IDORProtection::class,
    'file.security' => FileUploadSecurity::class,
    'admin.ip' => AdminIPRestriction::class,
    'session.security' => SessionSecurity::class,
]);
```

### Using Middleware in Routes

```php
// Login route with throttle
Route::post('/login', [LoginController::class, 'login'])
    ->middleware('throttle.login');

// Protected route
Route::middleware(['auth', 'session.security'])->group(function () {
    // Routes here
});

// Admin routes with IP restriction
Route::middleware(['auth', 'role:admin', 'admin.ip'])->group(function () {
    // Admin routes
});

// File upload with security
Route::post('/upload', [UploadController::class, 'store'])
    ->middleware('file.security');
```

---

## 📊 Security Logging

Security logs are stored in:
- `storage/logs/security.log` - Security events
- `storage/logs/audit.log` - User activity audit

View logs:
```bash
tail -f storage/logs/security.log
```

---

## 🚀 Production Checklist

Before deploying to production:

- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Enable `SESSION_SECURE_COOKIE=true`
- [ ] Configure HTTPS
- [ ] Set strong `APP_KEY`
- [ ] Configure `SECURITY_ADMIN_EMAIL`
- [ ] Review `ADMIN_IP_WHITELIST`
- [ ] Enable all security features in `.env`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Test all security features
- [ ] Review file permissions

---

## 📞 Security Contact

For security issues, contact: security@idnmenulis.com

---

## 📝 Changelog

### v1.0.0 (2026-02-12)
- Initial security implementation
- XSS, SQL Injection, CSRF protection
- Two-Factor Authentication
- Rate limiting & brute force protection
- Security logging & monitoring
- File upload security
- Session security
