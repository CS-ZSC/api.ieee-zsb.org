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

        // Ensure the response is JSON
        if (!$response->headers->has('Content-Type') || !str_contains($response->headers->get('Content-Type'), 'application/json')) {
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }
}
