<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    //
    public function createProfile(Request $request)
    {
        $validatedData = $request->validate([
            'bio' => 'required|string',
            'education' => 'nullable|string',
            'website_url' => 'nullable|url',
            'work' => 'nullable|string',
            'location' => 'nullable|string',
            'profile_image' => 'nullable|image',
        ]);

        try {
            // Process profile creation
            $profileData = [
                'bio' => $validatedData['bio'],
                'eduction' => $validatedData['eduction'],
                'website_url' => $validatedData['website_url'],
                'work' => $validatedData['work'],
                'location' => $validatedData['location'],
                'profile_image' => $validatedData['profile_image'],
                'user_id' => $request->header('user_id')
            ];

            $profile = Profile::create($profileData);
            return response()->json(['profile' => $profile], 201);
        } catch (\Exception $e) {
            Log::error('Profile creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Profile creation failed'], 500);
        }
    }
    public function getUserProfile(Request $request)
    {
        // $isUserFollow = DB::table('users')
        //     ->leftJoin("follow_users", "users.id", "=", "follow_users.user_id")
        //     ->where("follow_users.user_id", "=", $request->header("user_id"))
        //     ->first()->is_follow;
        // dd($isUserFollow);
        $profile = DB::table("profiles")
            ->leftJoin("users", "profiles.user_id", "=", "users.id")
            ->where("profiles.user_id", "=", $request->input("id"))
            ->select(
                "profiles.profile_image",
                "profiles.website_url",
                "profiles.location",
                "profiles.bio",
                "profiles.work",
                "profiles.education",
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
                "profiles.education",
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
// $imageUrl = null;
// if ($request->hasFile("profile_image")) {
//     $file = $request->file("profile_image");
//     $fileName = $file->getClientOriginalName();
//     $time = time();
//     $imageName = "{$request->header("user_id")}-{$time}-{$fileName}";
//     $imageUrl = "uploads/{$imageName}";

//     $file->move(public_path("uploads"), $imageUrl);
// }


// if ($imageUrl) {
//     $profileData["profile_image"] = $imageUrl;
// }