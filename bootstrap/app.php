<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\PublishScheduledArticles;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\XSSProtection;
use App\Http\Middleware\SQLInjectionProtection;
use App\Http\Middleware\CommandInjectionProtection;
use App\Http\Middleware\SessionSecurity;
use App\Http\Middleware\ThrottleLogin;
use App\Http\Middleware\IDORProtection;
use App\Http\Middleware\FileUploadSecurity;
use App\Http\Middleware\AdminIPRestriction;
use App\Http\Middleware\SEOMiddleware;
use App\Http\Middleware\RedirectMiddleware;
use App\Http\Middleware\ForceHttps;
use App\Http\Middleware\ArticleAccessControl;
use App\Http\Middleware\TrustedHosts;
use App\Http\Middleware\CheckRegistration;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware aliases
        $middleware->alias([
            'role' => CheckRole::class,
            'throttle.login' => ThrottleLogin::class,
            'idor.protection' => IDORProtection::class,
            'file.security' => FileUploadSecurity::class,
            'admin.ip' => AdminIPRestriction::class,
            'session.security' => SessionSecurity::class,
            'seo' => SEOMiddleware::class,
            'article.access' => ArticleAccessControl::class,
            'force.https' => ForceHttps::class,
            'trusted.hosts' => TrustedHosts::class,
            'check.registration' => CheckRegistration::class,
        ]);

        // Global middleware for all web requests
        $middleware->appendToGroup('web', [
            TrustedHosts::class,            // Validate trusted hosts
                // ForceHttps::class,              // Force HTTPS in production (Temporarily disabled to fix redirect loop)
            RedirectMiddleware::class,      // Handle URL redirects first
            SEOMiddleware::class,           // SEO optimizations
            SecurityHeaders::class,
            XSSProtection::class,
            SQLInjectionProtection::class,
            CommandInjectionProtection::class,
            PublishScheduledArticles::class,
        ]);

        // API middleware group
        $middleware->appendToGroup('api', [
            TrustedHosts::class,
            SecurityHeaders::class,
            SQLInjectionProtection::class,
            CommandInjectionProtection::class,
        ]);

        // Trust all proxies (Required for Nginx Proxy Manager / Cloudflare)
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle exceptions for JSON/AJAX requests
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                $status = 500;

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal.',
                        'errors' => $e->errors(),
                    ], 422);
                }

                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    $status = $e->getStatusCode();
                }

                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Silakan login terlebih dahulu.',
                    ], 401);
                }

                if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.',
                    ], 403);
                }

                return response()->json([
                    'success' => false,
                    'message' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan server.',
                ], $status);
            }
        });
    })->create();
