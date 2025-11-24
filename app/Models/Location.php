<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    
    protected $table = 'locations';
    
    protected $fillable = ['city_name'];

    public function originatingTrips()
    {
        return $this->hasMany(Trip::class, 'origin_id');
    }

    public function destinationTrips()
    {
        return $this->hasMany(Trip::class, 'destination_id');
    }
}
