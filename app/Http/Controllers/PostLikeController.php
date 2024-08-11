<?php

namespace App\Http\Controllers;

use App\Models\PostLike;
use Illuminate\Http\Request;

class PostLikeController extends Controller
{
    //
    public function likePost(Request $request)
    {
        $isExist = PostLike::where("post_id", "=", $request->input("post_id"))
            ->where("user_id", "=", $request->header("user_id"))
            ->first();

        // when user like the post first time
        if (!$isExist) {
            $postLike = PostLike::create([
                "post_id" => $request->input("post_id"),
                "user_id" => $request->header("user_id"),
                "is_like" => true
            ]);

            if ($postLike) {
                return response()->json([
                    "status" => "success",
                    "message" => "you have liked the post"
                ]);
            }
            return response()->json([
                "status" => "failed",
                "message" => "something is wrong!"
            ]);
        }

        // when user dislike the post
        if ($isExist->is_like == 1) {
            $postLike = PostLike::where("post_id", "=", $request->input("post_id"))
                ->where("user_id", "=", $request->header("user_id"))
                ->update([
                    "is_like" => false
                ]);

            if ($postLike) {
                return response()->json([
                    "status" => "success",
                    "message" => "you have disliked the post"
                ]);
            }
            return response()->json([
                "status" => "failed",
                "message" => "something is wrong!"
            ]);
        // when user again like the post
        } else {
            $postLike = PostLike::where("post_id", "=", $request->input("post_id"))
                ->where("user_id", "=", $request->header("user_id"))
                ->update([
                    "is_like" => true
                ]);
            if ($postLike) {
                return response()->json([
                    "status" => "success",
                    "message" => "you have again liked the post"
                ]);
            }
            return response()->json([
                "status" => "failed",
                "message" => "something is wrong!"
            ]);
        }



    }
}
