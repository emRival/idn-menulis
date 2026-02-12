<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;

class CheckRegistration
{
    public function handle(Request $request, Closure $next)
    {
        if (!Setting::registrationEnabled()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Pendaftaran saat ini ditutup oleh administrator.'
                ], 403);
            }

            return redirect()->route('login')
                ->with('status', 'Pendaftaran saat ini ditutup oleh administrator.');
        }

        return $next($request);
    }
}
