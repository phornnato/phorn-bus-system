<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoginController extends Controller
{
    // Show login form
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
{
    $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    $user = \DB::table('users_role')
        ->where('username', $request->username)
        ->first();

    if($user && \Hash::check($request->password, $user->password)){
        // Store user data in session
        session([
            'user_id' => $user->id,
            'username' => $user->username,
            'image' => $user->image,
            'role' => $user->role
        ]);

        // Redirect based on role
        if($user->role == 'Admin'){
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('dashboard');
        }
    }

    return back()->with('error', 'Invalid credentials');
}


    // Logout
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->flush();
        return redirect('/login');
    }
}
