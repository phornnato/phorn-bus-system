<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Passenger;
use App\Models\Trip;
use App\Models\TripPoint;
use App\Models\TripSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class BookingController extends Controller
{
     public function store(Request $request)
{
    DB::beginTransaction();
    
    try {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:trip_schedules,id',
            'seats' => 'required|array',
            'seats.*' => 'integer',
            'boarding_point_id' => 'required|exists:trip_points,id',
            'dropping_point_id' => 'required|exists:trip_points,id',
            'contact_email' => 'required|email',
            'contact_phone' => 'required',
            'total_price' => 'required|numeric',
            'payment_method' => 'required|string',
            'passengers' => 'required|array',
            'passengers.*.full_name' => 'required|string',
            'passengers.*.age' => 'required|integer|min:1|max:120',
            'passengers.*.nationality' => 'required|string',
            'passengers.*.gender' => 'required|string|in:Male,Female',
        ]);

        // Get user ID from session - using your session structure
        $user = Session::get('user');
        $userId = $user ? $user->id : null;

        // Check if points belong to correct types
        $boardingPoint = TripPoint::find($validated['boarding_point_id']);
        $droppingPoint = TripPoint::find($validated['dropping_point_id']);

        if (!$boardingPoint || $boardingPoint->type !== 'boarding') {
            throw new \Exception('Selected boarding point is invalid.');
        }

        if (!$droppingPoint || $droppingPoint->type !== 'dropping') {
            throw new \Exception('Selected dropping point is invalid.');
        }

        // Check if seats are available
        $existingBookedSeats = BookingSeat::where('schedule_id', $validated['schedule_id'])
            ->whereIn('seat_number', $validated['seats'])
            ->exists();

        if ($existingBookedSeats) {
            throw new \Exception('One or more selected seats are already booked.');
        }

        // Generate booking reference
        $bookingReference = 'BK' . date('Ymd') . strtoupper(uniqid());

        // Create booking
        $booking = Booking::create([
            'user_id' => $userId, 
            'schedule_id' => $validated['schedule_id'],
            'booking_reference' => $bookingReference,
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $validated['contact_phone'],
            'boarding_point_id' => $validated['boarding_point_id'],
            'dropping_point_id' => $validated['dropping_point_id'],
            'total_price' => $validated['total_price'],
            'payment_method' => $validated['payment_method'],
            'status' => 'confirmed',
        ]);

        // Create passengers and booking seats
        foreach ($validated['seats'] as $index => $seatNumber) {
            // Create passenger
            Passenger::create([
                'booking_id' => $booking->id,
                'seat_number' => $seatNumber,
                'full_name' => $validated['passengers'][$index]['full_name'],
                'gender' => $validated['passengers'][$index]['gender'],
                'age' => $validated['passengers'][$index]['age'],
                'nationality' => $validated['passengers'][$index]['nationality'],
            ]);

            // Create booking seat record
            BookingSeat::create([
                'schedule_id' => $validated['schedule_id'],
                'booking_id' => $booking->id,
                'seat_number' => $seatNumber,
            ]);
        }

        // Update available seats in trip schedule
        $tripSchedule = TripSchedule::find($validated['schedule_id']);
        $tripSchedule->available_seats -= count($validated['seats']);
        $tripSchedule->save();

       

        DB::commit();

        return response()->json([
            'success' => true,
            'booking_reference' => $bookingReference,
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'message' => 'Booking confirmed successfully!'
        ]);

         return redirect()->route('user.dashboard')
            ->with('success', 'Booking confirmed! Your booking reference is: ' . $booking->booking_reference);

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Booking failed: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Booking failed: ' . $e->getMessage()
        ], 500);
    }
}
    // Get booking details with relationships
    public function show($id)
    {
        $booking = Booking::with([
            'passengers',
            'schedule.trip',
            'boardingPoint',
            'droppingPoint',
            'user'
        ])->findOrFail($id);

        return response()->json([
            'booking' => $booking,
            'passengers' => $booking->passengers,
            'trip' => $booking->schedule->trip,
        ]);
    }

    public function showBookingPage(Request $request, $trip_id)
    {
        // 1. Validate the date parameter from the query string (e.g., ?date=2025-09-30)
        $searchDate = $request->input('date', Carbon::today()->toDateString());

        // 2. Fetch the trip and related data
        $trip = Trip::with(['origin', 'destination', 'points' => function ($query) {
                $query->orderBy('type')->orderBy('time'); // Order points logically
            }])
            ->findOrFail($trip_id);

        // 3. Get the schedule and booked seats for the specific date
        $schedule = $trip->schedules()
        ->whereDate('journey_date', $searchDate)
        ->first();


        if (!$schedule) {
            // If no schedule exists, we can't book. Redirect back or show an error.
            return redirect()->route('search')->with('error', 'Schedule not found for this date.');
        }

        $bookedSeats = DB::table('booked_seats')
            ->where('schedule_id', $schedule->id)
            ->pluck('seat_number')
            ->toArray();

        $boardingPoints = $trip->points->where('type', 'boarding')->sortBy('time');
        $droppingPoints = $trip->points->where('type', 'dropping')->sortBy('time');

        // Initial data for the steps
        $data = [
            'trip' => $trip,
            'schedule' => $schedule,
            'searchDate' => $searchDate,
            'bookedSeats' => $bookedSeats,
            'boardingPoints' => $boardingPoints,
            'droppingPoints' => $droppingPoints,
        ];

        return view('booking.index', $data);
    }
}
