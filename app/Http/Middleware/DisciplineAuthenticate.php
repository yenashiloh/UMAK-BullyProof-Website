<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class DisciplineAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('Session Data:', $request->session()->all());
    
        if (!session('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Unauthorized access. Please log in as a discipline admin.');
        }
    
        if (session('admin_role') !== 'discipline') {
            return redirect()->route('login')->with('error', 'Unauthorized access. Please log in as a discipline admin.');
        }
    
        return $next($request);
    }
    

    
}