<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;


class Authontroller extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return $this->errorResponse('Identifiants invalides', 401);
        }

        // Créer un token avec refresh token
        $token = $user->createToken('API Token')->accesToken;
        $refreshToken = $user->createToken('Refesh Token')->accesToken;

        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
        ], 200);
    }

    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required',
        ]);

        $user = Auth::guard('api')->user();

        if (!$user) {
            return $this->errorResponse('Token invalide', 401);
        }

        // Révoquer l'ancien token
        $user->tokens()->where('id', $request->refresh_token)->delete();

        // Créer un nouveau token
        $newToken = $user->createToken('API Token');

        return $this->successResponse([
            'access_token' => $newToken->accessToken,
            'refresh_token' => $newToken->token->id,
        ], 'Token rafraîchi avec succès');
    }
}
