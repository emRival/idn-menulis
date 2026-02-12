<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectMiddleware
{
    /**
     * Handle redirects from database
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        $path = '/' . ltrim($request->path(), '/');

        // Check for redirect
        $redirect = Redirect::findRedirect($path);

        if ($redirect) {
            // Record the hit
            $redirect->recordHit();

            // Perform redirect
            return redirect($redirect->to_url, $redirect->status_code);
        }

        return $next($request);
    }
}
