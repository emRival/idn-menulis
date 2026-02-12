<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IDORProtection
{
    /**
     * Models that require ownership verification.
     */
    protected array $protectedModels = [
        'article' => 'user_id',
        'comment' => 'user_id',
        'bookmark' => 'user_id',
        'notification' => 'user_id',
    ];

    /**
     * Roles that bypass IDOR check.
     */
    protected array $bypassRoles = ['admin'];

    /**
     * Handle IDOR protection.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Admin can access everything
        if (in_array($user->role, $this->bypassRoles)) {
            return $next($request);
        }

        // Check route parameters for IDOR
        foreach ($this->protectedModels as $model => $ownerField) {
            $parameter = $request->route($model);

            if ($parameter && is_object($parameter)) {
                // Model binding is used
                if (property_exists($parameter, $ownerField) || isset($parameter->$ownerField)) {
                    if ($parameter->$ownerField !== $user->id) {
                        // Check if user has permission through policy
                        if (!$this->userHasAccess($user, $parameter, $request->method())) {
                            $this->logIDORAttempt($request, $user, $model, $parameter);

                            abort(403, 'Anda tidak memiliki izin untuk mengakses resource ini.');
                        }
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * Check if user has access through other means (e.g., shared resources).
     */
    protected function userHasAccess($user, $model, string $method): bool
    {
        // Read-only access for public resources
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
            // Check if resource is public
            if (method_exists($model, 'isPublic') && $model->isPublic()) {
                return true;
            }

            // For articles - published articles are public
            if (get_class($model) === 'App\Models\Article') {
                if ($model->status === 'published') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Log IDOR attempt.
     */
    protected function logIDORAttempt(Request $request, $user, string $model, $resource): void
    {
        Log::channel('security')->warning('IDOR attempt detected', [
            'ip' => $request->ip(),
            'user_id' => $user->id,
            'user_email' => $user->email,
            'attempted_model' => $model,
            'attempted_resource_id' => $resource->id ?? 'unknown',
            'resource_owner_id' => $resource->user_id ?? 'unknown',
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
