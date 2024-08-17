<?php

namespace App\Http\Controllers;

use App\Models\FollowTag;
use Illuminate\Http\Request;

class FollowTagController extends Controller
{
    //
    public function followAndUnFollowTag(Request $request)
    {
        $isExist = FollowTag::where("tag_id", "=", $request->input("tag_id"))
            ->where("user_id", "=", $request->header("user_id"))
            ->first();
            
            // when user like the post first time
            if (!$isExist) {
                $postLike = FollowTag::create([
                    "tag_id" => $request->input("tag_id"),
                    "user_id" => $request->header("user_id"),
                    "is_follow" => true
                ]);
                
            if ($postLike) {
                return response()->json([
                    "status" => "success",
                    "message" => "you have followed the tag"
                ]);
            }
            return response()->json([
                "status" => "failed",
                "message" => "something is wrong!"
            ]);
        }

        // when user dislike the post
        if ($isExist->is_follow == 1) {
            $postLike = FollowTag::where("tag_id", "=", $request->input("tag_id"))
                ->where("user_id", "=", $request->header("user_id"))
                ->update([
                    "is_follow" => false
                ]);

            if ($postLike) {
                return response()->json([
                    "status" => "success",
                    "message" => "you have unFollowed the tag"
                ]);
            }
            return response()->json([
                "status" => "failed",
                "message" => "something is wrong!"
            ]);
        // when user again like the post
        } else {
            $postLike = FollowTag::where("tag_id", "=", $request->input("tag_id"))
                ->where("user_id", "=", $request->header("user_id"))
                ->update([
                    "is_follow" => true
                ]);
            if ($postLike) {
                return response()->json([
                    "status" => "success",
                    "message" => "you have again followed the tag"
                ]);
            }
            return response()->json([
                "status" => "failed",
                "message" => "something is wrong!"
            ]);
        }



    }
}
