<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SuperAdminAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Unauthorized access. Please log in as a super admin.');
        }
    
        if (session('admin_role') !== 'superadmin') {
            return redirect()->route('login')->with('error', 'Unauthorized access. Please log in as a super admin.');
        }
    
        return $next($request);
    }
}