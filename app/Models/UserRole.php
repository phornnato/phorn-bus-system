<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// CRITICAL: Must extend Authenticatable, not just Model
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;

class UserRole extends Authenticatable
{
    use HasFactory, Notifiable;

   
    protected $table = 'users_role';

    
    protected $fillable = [
        'username',
        'email',
        'password',
        'image',
        'role',
    ];

    
    protected $hidden = [
        'password',
    ];
}
