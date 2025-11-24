<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trip extends Model
{
    use HasFactory;

    protected $table = 'trips';

    protected $guarded = [];

    
    protected $casts = [
        'departure_time' => 'datetime:H:i',
        'arrival_time'   => 'datetime:H:i',
        'capacity' => 'integer',
        'base_price' => 'float',
    ];

    public function origin(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TripSchedule::class, 'trip_id'); 
    }

    
    public function points(): HasMany
    {
        return $this->hasMany(TripPoint::class, 'trip_id');
    }

     
    public function bookings()
    {
        return $this->hasManyThrough(
            Booking::class,
            TripSchedule::class,
            'trip_id', 
            'schedule_id', 
            'id', 
            'id' 
        );
    }

    // Get boarding points
    public function boardingPoints()
    {
        return $this->points()->boarding();
    }

    
    public function droppingPoints()
    {
        return $this->points()->dropping();
    }
    
}
