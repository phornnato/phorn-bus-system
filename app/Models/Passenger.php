<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory;

    protected $table = 'passengers';

    protected $fillable = [
        'booking_id',
        'seat_number',
        'full_name',
        'gender',
        'age',
        'nationality',
    ];

    protected $casts = [
        'age' => 'integer',
    ];

    
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    
    public function schedule()
    {
        return $this->hasOneThrough(
            TripSchedule::class,
            Booking::class,
            'id', 
            'id',
            'booking_id', 
            'schedule_id' 
        );
    }

    
    public function scopeMale($query)
    {
        return $query->where('gender', 'Male');
    }

    
    public function scopeFemale($query)
    {
        return $query->where('gender', 'Female');
    }

  
    public function getPassengerInfoAttribute()
    {
        return "{$this->full_name} (Seat: {$this->seat_number}, {$this->gender}, {$this->age} years)";
    }
}