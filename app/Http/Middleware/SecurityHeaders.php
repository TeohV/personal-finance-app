<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $viteDevServers = app()->environment('local')
            ? ' http://localhost:* http://127.0.0.1:* ws://localhost:* ws://127.0.0.1:*'
            : '';

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; ".
            "script-src 'self' 'unsafe-inline' cdn.jsdelivr.net{$viteDevServers}; ".
            "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net fonts.googleapis.com{$viteDevServers}; ".
            "font-src 'self' fonts.gstatic.com; ".
            "img-src 'self' data:; ".
            "connect-src 'self'{$viteDevServers};"
        );

        return $response;
    }
}
