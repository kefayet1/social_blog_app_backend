<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Mail\OTPMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PharIo\Manifest\Email;

class OTPController extends Controller
{
    //
    public function sentOTP(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email'
        ]);

        $otp = rand(1000, 9999);
        $user = User::where("email", "=", $validated['email'])->count();

        if ($user != 1) {
            return response()->json([
                "error" => "You don't have an account"
            ]);
        }

        if ($user == 1) {
            Mail::to($validated['email'])->send(new OTPMail($otp));
            User::where("email", "=", $validated['email'])->update(["otp" => $otp]);
        }

        return response()->json([
            "status" => "success",
            "message" => "4 Digit OTP Code has been sent to your email"
        ]);

    }

    public function verifyOTP(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'otp' => "required|string"
        ]);

        $user = User::where("email", "=", $validated['email'])
            ->where("otp", "=", $validated['otp']);

        $token = JWTToken::createToken($validated['email']);

        if ($user->count() != 1) {
            return response()->json([
                "status" => "failed",
                "message" => "you otp isn't correct"
            ]);
        } else {
            return response()->json([
                "status" => "success",
                "token" => $token
            ]);
        }
    }

}
