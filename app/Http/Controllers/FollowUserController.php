<?php

namespace App\Http\Controllers;

use App\Models\FollowUser;
use Illuminate\Http\Request;

class FollowUserController extends Controller
{
    public function followAndUnfollowUser(Request $request)
    {

        $isExist = FollowUser::where("user_id", "=", $request->header("user_id"))
            ->first();

        // when user like the post first time
        if (!$isExist) {
            $postLike = FollowUser::create([
                "user_id" => $request->header("user_id"),
                "following_user_id" => $request->input("following_user_id"),
                "is_follow" => true
            ]);

            if ($postLike) {
                return response()->json([
                    "status" => "success",
                    "message" => "you have following the publisher"
                ]);
            }
            return response()->json([
                "status" => "failed",
                "message" => "something is wrong!"
            ]);
        }

        // when user dislike the post
        if ($isExist->is_follow == 1) {
            $postLike = FollowUser::where("user_id", "=", $request->header("user_id"))
                ->where("following_user_id", "=", $request->input("following_user_id"))
                ->update([
                    "is_follow" => false
                ]);

            if ($postLike) {
                return response()->json([
                    "status" => "success",
                    "message" => "you have unfollowed the publisher"
                ]);
            }
            return response()->json([
                "status" => "failed",
                "message" => "something is wrong!"
            ]);
            // when user again like the post
        } else {
            $postLike = FollowUser::where("user_id", "=", $request->header("user_id"))
                ->where("following_user_id", "=", $request->input("following_user_id"))
                ->update([
                    "is_follow" => true
                ]);
            if ($postLike) {
                return response()->json([
                    "status" => "success",
                    "message" => "you have again followed the publisher"
                ]);
            }
            return response()->json([
                "status" => "failed",
                "message" => "something is wrong!"
            ]);
        }




    }
}
