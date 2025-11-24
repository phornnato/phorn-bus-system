<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{

    public function indexRecord(){
           $users = UserInfo::all();
           return view('admin.user_info.index', compact('users'));

    }
    // Show register page
    public function showRegister()
    {
        return view('user.register');
    }

    // Handle registration
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:user_info',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = UserInfo::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        Session::put('user', $user);

        return redirect()->route('user.login')->with('success', 'Registered successfully!');
    }

    // Show login page
    public function showLogin()
    {
        return view('user.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = UserInfo::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Invalid email or password');
        }

        Session::put('user', $user);

        return redirect()->route('user.dashboard')->with('success', 'Welcome back!');
    }

    // Dashboard
   public function dashboard()
{
    if (!Session::has('user')) {
        return redirect()->route('user.login')->with('error', 'Please login first.');
    }

    $user = Session::get('user');
    
    // Get user's bookings with all related data
    $bookings = Booking::with([
            'schedule.trip.origin',
            'schedule.trip.destination', 
            'passengers', 
            'boardingPoint', 
            'droppingPoint'
        ])
        ->where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();

    // Calculate statistics
    $totalBookings = $bookings->count();
    $confirmedBookings = $bookings->where('status', 'confirmed')->count();
    $pendingBookings = $bookings->where('status', 'pending')->count();
    $cancelledBookings = $bookings->where('status', 'cancelled')->count();

    return view('user.dashboard', compact(
        'user', 
        'bookings', 
        'totalBookings', 
        'confirmedBookings', 
        'pendingBookings',
        'cancelledBookings'
    ));
}

    // Logout
    public function logout()
    {
        Session::forget('user');
        return redirect()->route('user.login')->with('success', 'Logged out successfully.');
    }
}
