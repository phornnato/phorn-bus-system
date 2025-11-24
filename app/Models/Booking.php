<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'schedule_id',
        'booking_reference',
        'contact_email',
        'contact_phone',
        'boarding_point_id',
        'dropping_point_id',
        'total_price',
        'payment_method',
        'status',
    ];

     protected $casts = [
        'total_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(UserInfo::class, 'user_id');
    }

    public function schedule()
    {
        return $this->belongsTo(TripSchedule::class, 'schedule_id');
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class, 'booking_id');
    }

    public function seats()
    {
        return $this->hasMany(BookingSeat::class, 'booking_id');
    }

    public function boardingPoint()
    {
        return $this->belongsTo(TripPoint::class, 'boarding_point_id');
    }

    public function droppingPoint()
    {
        return $this->belongsTo(TripPoint::class, 'dropping_point_id');
    }

    public function getSeatsAttribute()
    {
        return $this->passengers->pluck('seat_number')->toArray();
    }

    // Get passenger count
    public function getPassengerCountAttribute()
    {
        return $this->passengers->count();
    }
}