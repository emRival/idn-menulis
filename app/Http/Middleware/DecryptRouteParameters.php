<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\EncryptionService;
use Symfony\Component\HttpFoundation\Response;

class DecryptRouteParameters
{
    protected EncryptionService $encryption;

    public function __construct(EncryptionService $encryption)
    {
        $this->encryption = $encryption;
    }

    /**
     * Automatically decode hashid route parameters.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // List of route parameters that should be decoded
        $encryptedParams = ['article', 'comment', 'user'];

        foreach ($encryptedParams as $param) {
            if ($request->route($param)) {
                $hash = $request->route($param);

                // Skip if it's already a numeric ID (backward compatibility)
                if (is_numeric($hash)) {
                    continue;
                }

                $decodedId = $this->encryption->decodeId($hash);

                if ($decodedId === null) {
                    abort(404, 'Resource not found');
                }

                $request->route()->setParameter($param, $decodedId);
            }
        }

        return $next($request);
    }
}
