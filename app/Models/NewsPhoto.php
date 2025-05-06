<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'news_id',
        'photo_path',
    ];

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
