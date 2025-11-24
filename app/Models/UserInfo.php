<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    protected $table = 'user_info';

    protected $fillable = [
        'name', 'email', 'password', 'phone',
    ];

    protected $hidden = [
        'password',
    ];

      
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }
}
