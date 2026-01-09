<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
       public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is admin
        if (!auth()->check() || auth()->user()->role_type !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
            // Or redirect: return redirect('/')->with('error', 'Unauthorized access');
        }

        return $next($request);
    }
}
