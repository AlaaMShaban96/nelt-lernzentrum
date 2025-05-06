<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'level',
        'start_date',
        'seats',
        'available_seats',
    ];

    protected $casts = [
        'start_date' => 'date',
        'seats' => 'integer',
        'available_seats' => 'integer',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}
