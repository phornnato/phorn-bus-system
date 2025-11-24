<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingSeat extends Model
{
    use HasFactory;

    protected $table = 'booked_seats';

    protected $fillable = [
        'schedule_id',
        'booking_id',
        'seat_number',
    ];

    // Seat belongs to a booking
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    // Seat belongs to a schedule
    public function schedule()
    {
        return $this->belongsTo(TripSchedule::class, 'schedule_id');
    }
}
