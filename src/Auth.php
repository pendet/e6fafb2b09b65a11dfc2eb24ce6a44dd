<?php

namespace App;

use App\Models\User;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth
{
    public static function user(): User
    {
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return null;
        }

        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        preg_match('/Bearer\s(\S+)/', $authHeader, $matches);

        if (!isset($matches[1])) {
            return false;
        }

        $token = $matches[1];
        if (!self::decodeToken($token)) {
            return null;
        }

        $authenticated = self::authenticate($token);
        if (!$authenticated['status']) {
            return null;
        }

        return $authenticated['user'];
    }

    public static function authenticate(string $token)
    {
        $decode = self::decodeToken($token);
        if (isset($decode['status']) && !$decode['status']) {
            return $decode;
        }
        $email = $decode['email'];
        $exp = $decode['exp'];

        if ($exp < now()->timestamp) {
            return ['status' => false, 'message' => 'Expired Token'];
        }

        $user = User::where('email', $email)->first();

        if (empty($user)) {
            return ['status' => false, 'message' => 'Unknown User'];
        }

        return ['status' => true, 'user' => $user];
    }

    public static function verifyToken()
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        preg_match('/Bearer\s(\S+)/', $authHeader, $matches);

        if (!isset($matches[1])) {
            return false;
        }

        $token = $matches[1];
        $decode = self::decodeToken($token);
        if (isset($decode['status']) && !$decode['status']) {
            return $decode;
        }
        return !empty($decode) ?? $decode;
    }

    public static function decodeToken(string $token)
    {
        try {
            $result = (array) JWT::decode($token, new Key($_ENV['SECRET'], 'HS256'));
            return $result;
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public static function generateToken(User $user)
    {
        $payload = [
            'name' => $user->name,
            'email' => $user->email,
            'iat' => now()->timestamp,
            'exp' => now()->addMinutes(60)->timestamp
        ];

        $token = JWT::encode($payload, $_ENV['SECRET'], 'HS256');

        return $token;
    }
}
