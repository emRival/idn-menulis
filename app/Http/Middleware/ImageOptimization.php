<?php

namespace App\Http\Middleware;

use Closure;

class ImageOptimization
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof \Illuminate\Http\Response) {
            $content = $response->getContent();

            // Add lazy loading to images
            $content = preg_replace(
                '/<img((?!loading)[^>]*)>/i',
                '<img$1 loading="lazy">',
                $content
            );

            // Add alt if missing
            $content = preg_replace(
                '/<img(?![^>]*alt=)[^>]*>/i',
                '<img alt="IDN Menulis Image"$0',
                $content
            );

            $response->setContent($content);
        }

        return $response;
    }
}
