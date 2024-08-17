<?php

namespace App\Http\Controllers;

use App\Models\FollowTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function getFollowingTag(Request $request)
    {
        $tags = DB::table('follow_tags')
            ->rightJoin("tags", "follow_tags.tag_id", "=", "tags.id")
            ->where("follow_tags.user_id", "=", $request->header("user_id"))
            ->where("is_follow", "=", true)
            ->select("tags.id", "tags.title")
            ->groupBy("tags.id", "tags.title")
            ->get();
        if ($tags) {
            return response()->json([
                'status' => 'success',
                'data' => $tags
            ]);
        }

        return response()->json([
            'status' => 'failed',
            'data' => "something is wrong!"
        ]);
    }
}
