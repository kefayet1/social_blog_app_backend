<?php

namespace App\Http\Controllers;

use App\Models\SavePosts;
use Illuminate\Http\Request;

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
            $postLike = SavePosts::create([
                "post_id" => $request->input("post_id"),
                "user_id" => $request->header("user_id"),
                "is_save" => true
            ]);

            if ($postLike) {
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
            $postLike = SavePosts::where("post_id", "=", $request->input("post_id"))
                ->where("user_id", "=", $request->header("user_id"))
                ->update([
                    "is_save" => false
                ]);

            if ($postLike) {
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
            $postLike = SavePosts::where("post_id", "=", $request->input("post_id"))
                ->where("user_id", "=", $request->header("user_id"))
                ->update([
                    "is_save" => true
                ]);
            if ($postLike) {
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
}
