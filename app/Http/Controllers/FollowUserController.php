<?php

namespace App\Http\Controllers;

use Faker\Core\Number;
use App\Models\FollowUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FollowUserController extends Controller
{
    public function followAndUnfollowUser(Request $request)
    {

        $isExist = FollowUser::where("user_id", "=", $request->header("user_id"))
            ->where("following_user_id", "=", $request->input("following_user_id"))
            ->first();
        // when user like the post first time
        if (!$isExist) {
            $postLike = FollowUser::create([
                "user_id" => $request->header("user_id"),
                "following_user_id" => $request->input("following_user_id"),
                "is_follow" => true,
            ]);

            if ($postLike) {
                return response()->json([
                    "status" => "success",
                    "message" => "you are following the publisher"
                ]);
            }
            return response()->json([
                "status" => "failed",
                "message" => "something is wrong!",
                "header" => $request->header("user_id"),
                "follow" => $request->input("following_user_id")
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
                "message" => "something is wrong!",
                "header" => $request->header("user_id"),
                "follow" => $request->input("following_user_id")
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
                "message" => "something is wrong!",

            ]);
        }
    }

    public function getFollowers(Request $request)
    {
        $followers = DB::table("follow_users")
            ->leftJoin("users", "follow_users.user_id", "=", "users.id")
            ->leftJoin("profiles", "follow_users.user_id", "=", "profiles.user_id")
            ->where("follow_users.following_user_id", "=", $request->header("user_id"))
            ->select(
                "profiles.profile_image",
                "users.id",
                "users.email",
                "users.name"
            )
            ->get();

        if ($followers) {
            return response()->json([
                "status" => "success",
                "totalFollowers" => $followers->count(),
                "data" => $followers
            ], 200);
        }
        return response()->json([
            "status" => "failed",
            "message" => "something is wrong!",

        ], 404);
    }

    public function getFollowing(Request $request)
    {
        $following = DB::table("follow_users")
            ->leftJoin("users", "follow_users.following_user_id", "users.id")
            ->leftJoin("profiles", "follow_users.following_user_id", "profiles.user_id")
            ->where("follow_users.user_id", "=", $request->header("user_id"))
            ->where('is_follow', "=", true)
            ->select(
                "profiles.profile_image",
                "follow_users.following_user_id",
                "users.email",
                "users.name"
            )
            ->get();

        if ($following) {
            return response()->json([
                "status" => "success",
                "totalFollowing" => $following->count(),
                "data" => $following
            ], 200);
        }
        return response()->json([
            "status" => "failed",
            "message" => "something is wrong!",

        ], 404);
    }

}
