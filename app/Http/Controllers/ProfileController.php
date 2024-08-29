<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    //
    public function createProfile(Request $request)
    {
        $imageUrl = null;
        if ($request->hasFile("profile_image")) {
            $file = $request->file("profile_image");
            $fileName = $file->getClientOriginalName();
            $time = time();
            $imageName = "{$request->header("user_id")}-{$time}-{$fileName}";
            $imageUrl = "uploads/{$imageName}";

            $file->move(public_path("uploads"), $imageUrl);
        }

        $profileData = [
            "website_url" => $request->input("website_url"),
            "location" => $request->input("location"),
            "bio" => $request->input("bio"),
            "work" => $request->input("work"),
            "eduction" => $request->input("eduction"),
            "user_id" => $request->header("user_id")
        ];

        if ($imageUrl) {
            $profileData["profile_image"] = $imageUrl;
        }

        $profile = Profile::create($profileData);

        if (!$profile) {
            return response()->json([
                "status" => "failed",
                "message" => "something went wrong!"
            ]);
        }
        return response()->json([
            "status" => "success",
            "message" => "your profile is created"
        ]);
    }

    public function getUserProfile(Request $request)
    {
        $profile = DB::table("profiles")
            ->leftJoin("users", "profiles.user_id", "=", "users.id")
            ->where("profiles.user_id", "=", $request->input("id"))
            ->select(
                "profiles.profile_image",
                "profiles.website_url",
                "profiles.location",
                "profiles.bio",
                "profiles.work",
                "profiles.eduction",
                "profiles.website_url",
                "users.name",
                "users.email",
                "users.created_at"
            )
            ->groupBy(
                "profiles.profile_image",
                "profiles.website_url",
                "profiles.location",
                "profiles.bio",
                "profiles.work",
                "profiles.eduction",
                "profiles.website_url",
                "users.name",
                "users.email",
                "users.created_at"
            )
            ->first();

        if (!$profile) {
            return response()->json([
                "status" => "failed",
                "message" => "something went wrong!"
            ]);
        }
        return response()->json([
            "status" => "success",
            "data" => $profile
        ]);
    }
}
