<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = ['text', 'is_seen', 'type', 'user_id', 'actor_id',"post_id", "tag_id"];
}
