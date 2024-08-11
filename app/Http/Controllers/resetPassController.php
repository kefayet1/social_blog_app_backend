<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Models\User;
use Illuminate\Http\Request;

class resetPassController extends Controller
{
    //
    function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => "required|string",
            "password" => "required|string|min:8"
        ]);

        $token = JWTToken::verifyToken($validated['token']);

        if (!$token) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid or expired token'
            ], 400);
        }


        $user = User::where("email", "=", $token->userEmail);

        // Check if user exists
        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'User not found'
            ], 404);
        }

        // Update the user's password
        $user->update(["password" => $validated['password']]);
        return response()->json([
            'status' => 'success',
            'message' => 'Your password has been changed'
        ], 200);

    }
}
