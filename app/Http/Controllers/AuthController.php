<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * @group Authentication
 *
 * APIs for authenticating users
 */
class AuthController extends Controller {

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
                'error' => $validator->errors(),
                'code' => 'validation_error',
            ], 422);
        }

        if (!Auth::attempt($data)) {
            return response()->json([
                'error' => 'Invalid credentials',
                'code' => 'invalid_credentials',
            ], 401);
        }

        $user = Auth::user();

        // Check if user is blocked or needs verification

        if ($user->blocked) {
            return response()->json([
                'error' => 'User is blocked',
                'code' => 'blocked',
            ], 401);
        }

        if ($user->isVerified()) {
            $user->sendVerifyEmail();
            return response()->json([
                'error' => 'User needs verification',
                'code' => 'verification_required',
            ], 401);
        }

        // Check if the user already has a token
        if ($user->tokens()->count() > 0) {
            // Delete the user's token
            $user->tokens()->delete();
        }

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
                'code' => 'no_user',
            ], 400);
        }
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

}
