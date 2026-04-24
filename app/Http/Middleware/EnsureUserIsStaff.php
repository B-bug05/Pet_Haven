<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if user is logged in AND has the staff/admin role
        if (auth()->check() && auth()->user()->isStaff()) {
            return $next($request); // Let them pass!
        }

        // 2. If they are a regular adopter (or guest), kick them out
        // You can redirect them to the dashboard with an error message, or show a 403 Forbidden page.
        abort(403, 'Unauthorized action. Staff access required.');
    }
}