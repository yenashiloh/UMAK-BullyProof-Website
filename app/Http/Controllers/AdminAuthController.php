<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use MongoDB\Client;

class AdminAuthController extends Controller
{
    //show login form
    public function showLoginForm()
    {
        if (session('admin_logged_in')) {
            $role = session('admin_role');
            
            if ($role === 'discipline') {
                return redirect()->route('admin.dashboard');
            } elseif ($role === 'guidance') {
                return redirect()->route('guidance.dashboard');
            }
        }
        return view('login');
    }

    //login post 
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $client = new Client(env('MONGODB_URI'));
        $adminCollection = $client->bullyproof->admins;
    
        $admin = $adminCollection->findOne(['email' => $request->email]);
    
        if ($admin && Hash::check($request->password, $admin->password)) {
            session([
                'admin_logged_in' => true, 
                'admin_id' => (string)$admin->_id,
                'admin_role' => $admin->role
            ]);
    
            if ($admin->role === 'discipline') {
                return redirect()->route('admin.dashboard');
            } elseif ($admin->role === 'guidance') {
                return redirect()->route('guidance.dashboard'); 
            }
        } else {
            return back()->withErrors(['email' => 'Incorrect email or password. Please try again.']);
        }
    }

    //logout
    public function logoutAdmin(Request $request)
    {
        Session::flush(); 
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return response()->json(['success' => true, 'message' => 'Logged out successfully']);
    }

    public function logoutGuidance(Request $request)
    {
        Session::flush(); 
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return response()->json(['success' => true, 'message' => 'Logged out successfully']);
    }
}
