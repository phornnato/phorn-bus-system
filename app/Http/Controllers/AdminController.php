<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\UserInfo;
use App\Models\Trip;

class AdminController extends Controller
{
public function dashboard()
{

    // Overall statistics
    $totalUsers = UserInfo::count();
    $totalBookings = Booking::count();
    $totalTrips = Trip::count();
    $totalRevenue = Booking::where('status', 'confirmed')->sum('total_price');
    
    // Get ALL bookings from ALL users
    $allBookings = Booking::with([
            'user',
            'schedule.trip.origin',
            'schedule.trip.destination', 
            'passengers', 
            'boardingPoint', 
            'droppingPoint'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

    // Booking status statistics
    $confirmedBookings = Booking::where('status', 'confirmed')->count();
    $pendingBookings = Booking::where('status', 'pending')->count();
    $cancelledBookings = Booking::where('status', 'cancelled')->count();

    return view('admin.dashboard', compact(
        'totalUsers',
        'totalBookings', 
        'totalTrips',
        'totalRevenue',
        'allBookings',
        'confirmedBookings',
        'pendingBookings',
        'cancelledBookings'
    ));
}

public function booking()
{
    $allBookings = Booking::with([
            'user',
            'schedule.trip.origin',
            'schedule.trip.destination', 
            'passengers', 
            'boardingPoint', 
            'droppingPoint'
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(15); // Change to paginate()

    return view('admin.recordBooking.index', compact('allBookings'));
}

public function confirmBooking($id)
{
    $booking = Booking::findOrFail($id);
    $booking->update(['status' => 'confirmed']);
    
    return redirect()->back()->with('success', 'Booking confirmed successfully.');
}

public function cancelBooking($id)
{
    $booking = Booking::findOrFail($id);
    $booking->update(['status' => 'cancelled']);
    
    return redirect()->back()->with('success', 'Booking cancelled successfully.');
}

}