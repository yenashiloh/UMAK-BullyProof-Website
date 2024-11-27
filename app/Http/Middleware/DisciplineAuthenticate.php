<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisciplineAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure that the admin is logged in
        if (!session('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Unauthorized access. Please log in as an admin.');
        }

        // Check if the user has the 'discipline' or 'superadmin' role
        $role = session('admin_role');
        if ($role !== 'discipline' && $role !== 'superadmin') {
            return redirect()->route('login')->with('error', 'Unauthorized access. You must be a discipline admin.');
        }

        return $next($request);
    }
}