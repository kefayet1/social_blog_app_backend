<?php

namespace App\Http\Middleware;

use App\Helper\JWTToken;
use App\Models\User;
use Closure;
use Exception;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenVerificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->input("token");
        try {
            $email = JWTToken::verifyToken($token)->userEmail;
            if (!$email) {
                throw new Exception("token is not valid");
            }
            $user = User::where("email", $email)->first(); // 

            if (!$user) {
                throw new Exception("User not found");
            }

            $id = $user->id; 
            $name = $user->name;
            $request->headers->set("email", $email);
            $request->headers->set("user_id", $id);
            $request->headers->set("name", $name);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized', 'message' => get_class($e)], 401);
        }
        return $next($request);
    }
}
