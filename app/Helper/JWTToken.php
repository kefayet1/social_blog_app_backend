<?php
namespace App\Helper;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTToken
{
    static function createToken($userEmail)
    {
        $key = env("JWT_KEY");

        $payload = [
            'iss' => "laravel-token",
            'ias' => time(),
            'exp' => time() * 60 * 60,
            'userEmail' => $userEmail
        ];

        $jwt = JWT::encode($payload, $key, "HS256");
        return $jwt;
    }

    static function verifyToken($token)
    {
        $key = env("JWT_KEY");
        try {
            // Decode the token
            $jwt = JWT::decode($token, new Key($key, 'HS256'));
            return $jwt;
        } catch (Exception $e) {
            // Handle decoding exceptions
            throw new Exception('Token verification failed: ' . $e->getMessage());
        }
    }
}