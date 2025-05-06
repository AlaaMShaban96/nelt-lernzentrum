<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'logo',
        'email',
        'phone_numbers',
        'location',
        'cover_photo',
        'welcome_title',
        'welcome_description',
    ];

    protected $casts = [
        'phone_numbers' => 'array',
    ];
}
