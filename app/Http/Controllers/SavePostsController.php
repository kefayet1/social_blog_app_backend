<?php

namespace App\Http\Controllers;

use App\Models\SavePosts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SavePostsController extends Controller
{
    //
    public function savePost(Request $request)
    {
        $isExist = SavePosts::where("post_id", "=", $request->input("post_id"))
            ->where("user_id", "=", $request->header("user_id"))
            ->first();

        // when user like the post first time
        if (!$isExist) {
            $savePost = SavePosts::create([
                "post_id" => $request->input("post_id"),
                "user_id" => $request->header("user_id"),
                "is_save" => true
            ]);

            if ($savePost) {
                return response()->json([
                    "status" => "success",
                    "message" => "You have bookmarked the post"
                ]);
            }
            return response()->json([
                "status" => "failed",
                "message" => "something is wrong!"
            ]);
        }

        // when user dislike the post
        if ($isExist->is_save == 1) {
            $savePost = SavePosts::where("post_id", "=", $request->input("post_id"))
                ->where("user_id", "=", $request->header("user_id"))
                ->update([
                    "is_save" => false
                ]);

            if ($savePost) {
                return response()->json([
                    "status" => "success",
                    "message" => "you have removed from bookmark the post"
                ]);
            }
            return response()->json([
                "status" => "failed",
                "message" => "something is wrong!"
            ]);
            // when user again like the post
        } else {
            $savePost = SavePosts::where("post_id", "=", $request->input("post_id"))
                ->where("user_id", "=", $request->header("user_id"))
                ->update([
                    "is_save" => true
                ]);
            if ($savePost) {
                return response()->json([
                    "status" => "success",
                    "message" => "you have again bookmarked the post"
                ]);
            }
            return response()->json([
                "status" => "failed",
                "message" => "something is wrong!"
            ]);
        }
    }

    public function getSavePost(Request $request) {
        $posts = DB::table("posts")
        ->leftJoin("save_posts", "posts.id", "=", "save_posts.post_id")
        ->leftJoin("profiles", "posts.user_id", "=", "profiles.user_id")
        ->leftJoin("users", "posts.user_id", "=", "users.id")
        ->where("save_posts.user_id", "=", $request->header("user_id"))
        ->select("posts.*", "profiles.profile_image", "users.name")
        ->get();

        return $posts;
    }

}
