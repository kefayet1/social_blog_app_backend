<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //

    public function login(Request $request)
    {
        $validated = $request->validate([
            "email" => "required|email|max:270",
            "password" => "required|min:8|string"
        ]);

        $user = User::where("email", "=", $validated['email'])
            ->where("password", "=", $validated['password'])->first();

        $profile_image = Profile::where("user_id", "=", $user->id)->first('profile_image')['profile_image'];

        if (!$user) {
            return response()->json([
                "status" => "failed",
                "error" => "You email and password isn't correct"
            ]);
        }

        $token = JWTToken::createToken($validated['email']);
        $userRole = $user->getRoleNames();

        return response()->json([
            "status" => "success",
            "token" => $token,
            "id" => $user->id,
            "user_role" => $userRole,
            "profile_image" => $profile_image,
            "name" => $user->name,
            "email" => $user->email
        ]);
    }

    public function registration(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:270",
            "email" => "required|email|max:270",
            "password" => "required|min:8|string"
        ]);

        $user = User::create([
            "name" => $validated['name'],
            "email" => $validated['email'],
            "password" => $validated['password']
        ]);


        if (!$user) {
            return response()->json([
                "status" => "failed",
                "message" => "Something is wrong in your email and password"
            ]);
        }else{
            $user->assignRole('user');
            $role = $user->getRoleNames();
        }

        return response()->json([
            "status" => "success",
            "message" => "Your account is created",
            "role" => $role
        ]);
    }
}
