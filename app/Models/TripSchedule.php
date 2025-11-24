<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripSchedule extends Model
{
    use HasFactory;

    protected $table = 'trip_schedules';

    protected $fillable = [
        'trip_id',
        'journey_date',
        'available_seats'
    ];

    protected $casts = [
        'journey_date' => 'date',
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
    ];

  
    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

   
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'schedule_id');
    }

    // Relationship with Passengers through Bookings
    public function passengers()
    {
        return $this->hasManyThrough(
            Passenger::class,
            Booking::class,
            'schedule_id', 
            'booking_id', 
            'id', 
            'id' 
        );
    }

    
    public function getBookedSeatsAttribute()
    {
        return $this->passengers()->pluck('seat_number')->toArray();
    }

    
    public function isSeatAvailable($seatNumber)
    {
        return !in_array($seatNumber, $this->booked_seats);
    }

    
    public function getAvailableSeatsCountAttribute()
    {
        return $this->available_seats - $this->passengers()->count();
    }

    
    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class, 'schedule_id');
    }

    public function show($id)
    {
        
        $tripSchedule = TripSchedule::with('trip')->findOrFail($id);

        return view('trip.show', compact('tripSchedule'));
    }
}
