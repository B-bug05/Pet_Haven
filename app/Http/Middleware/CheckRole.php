<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. If the user matches the required role, let them in!
        if (auth()->check() && auth()->user()->role === $role) {
            return $next($request);
        }

        // 2. If they are logged in but have the WRONG role, instantly bounce them to their correct portal.
        if (auth()->check()) {
            $actualRole = auth()->user()->role;
            if ($actualRole === 'admin') return redirect()->route('admin.dashboard');
            if ($actualRole === 'staff') return redirect()->route('staff.dashboard');
            return redirect()->route('dashboard'); // Adopter fallback
        }

        // 3. If they aren't logged in at all, send to login
        return redirect()->route('login');
    }
}