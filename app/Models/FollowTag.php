<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowTag extends Model
{
    use HasFactory;
    protected $fillable = ["user_id", "tag_id", "is_follow"];
}
