<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // If the user is not authorized or is not an administrator, we deny access
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }

        return $next($request); // Allow access if user is administrator
    }
}






