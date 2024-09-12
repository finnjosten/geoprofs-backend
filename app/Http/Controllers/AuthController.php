<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request) {
        $data = $request->only('email', 'password');

        // Define validation rules
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return a JSON response with validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!Auth::attempt($data)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('authToken', ['*'], now()->addDay())->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request) {
        if (!$request->user()) {
            return response()->json([
                'error' => 'No user logged in',
            ], 400);
        }
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function register(Request $request) {
    }
}
