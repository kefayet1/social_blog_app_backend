<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $casts = [
        'active' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $fillable = ['title','body','published_at','thumbnail','active',"user_id"];
}
