<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = [
        'full_name',
        'phone',
        'address',
        'course_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
