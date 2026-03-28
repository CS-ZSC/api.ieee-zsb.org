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

        // Don't force JSON for documentation endpoint
        if ($request->is('api/documentation')) {
            return $response;
        }

        // Force JSON content type for all other API routes
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
