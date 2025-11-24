<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripPoint extends Model
{
    use HasFactory;

    protected $table = 'trip_points';

    protected $fillable = [
        'trip_id',
        'type',
        'time',
        'name',
        'address',
        'map',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'time' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    
    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    
    public function boardingBookings()
    {
        return $this->hasMany(Booking::class, 'boarding_point_id');
    }

    
    public function droppingBookings()
    {
        return $this->hasMany(Booking::class, 'dropping_point_id');
    }

    
    public function scopeBoarding($query)
    {
        return $query->where('type', 'boarding');
    }

    
    public function scopeDropping($query)
    {
        return $query->where('type', 'dropping');
    }

    
    public function getPointInfoAttribute()
    {
        return "{$this->time->format('H:i')} - {$this->name} ({$this->address})";
    }
}