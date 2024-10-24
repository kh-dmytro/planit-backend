<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     title="Authentication API",
 *     version="1.0.0",
 *     description="API for user authentication including login and logout."
 * )
 */
/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     in="header",
 *     name="Authorization",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class AuthController extends Controller
{
 
    public function register(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    // Создаем нового пользователя
    $user = User::create([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'password' => Hash::make($validatedData['password']),
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
    ], 201);
}

     
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
    
            $user = User::where('email', $request->email)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid login credentials'], 401);
            }
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    

    public function logout(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }
    
        $user->currentAccessToken()->delete();
    
        return response()->json(['message' => 'Successfully logged out']);
    }
    
}

