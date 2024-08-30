<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //

    public function getNotification()
    {
        return User::count();
    }
}
