<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        $response = $next($request);

        // Force JSON content type regardless of what's already set
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
