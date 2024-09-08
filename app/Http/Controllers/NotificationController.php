<?php

namespace App\Http\Controllers;

use Event;
use App\Models\Post;
use App\Events\SendMail;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Events\SendNotification;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    //
    public function getNotification(Request $request)
    {

        $notification = DB::table("notifications")
            ->Join("users", "notifications.actor_id", "=", "users.id")
            ->Join("posts", "notifications.post_id", "=", "posts.id")
            ->join("tags", "notifications.tag_id", "=", "tags.id")
            ->where('notifications.user_id', "=", 3)
            ->select(
                "notifications.id",
                "posts.title",
                "users.name",
                "notifications.is_seen",
                "notifications.type",
                "notifications.user_id",
                "notifications.tag_id",
                "notifications.post_id",
                "notifications.created_at",
                "tags.title as tag_title"
            )
            ->groupBy(
                "notifications.id",
                "posts.title",
                "users.name",
                "notifications.is_seen",
                "notifications.type",
                "notifications.user_id",
                "notifications.tag_id",
                "notifications.post_id",
                "notifications.created_at",
                "tags.title"
            )
            ->get();


        return $notification;
    }
}
