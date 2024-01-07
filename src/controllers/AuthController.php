<?php

namespace App\Controllers;

use App\Auth;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController
{
    public function register(Request $request)
    {
        if (empty($request->name)) {
            return new JsonResponse([
                'message' => 'name required'
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        if (empty($request->email)) {
            return new JsonResponse([
                'message' => 'email required'
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        if (empty($request->password)) {
            return new JsonResponse([
                'message' => 'password required'
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => password_hash($request->password, PASSWORD_BCRYPT)
            ]);

            return new JsonResponse([
                'message' => 'user created',
                'user' => $user
            ]);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => $e->getPrevious()->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(Request $request)
    {
        if (empty($request->email) || empty($request->password)) {
            return new JsonResponse([
                'message' => 'email or password wrong'
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $user = User::where('email', $request->email)->first();
        if (empty($user) || !password_verify($request->password, $user->password)) {
            return new JsonResponse([
                'message' => 'credential wrong'
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $token = Auth::generateToken($user);

        return new JsonResponse([
            'message' => 'user logedin',
            'user' => $user,
            'token' => $token
        ]);
    }
}
