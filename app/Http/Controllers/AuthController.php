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

        ////// DISABLED //////
        // Check if user is blocked or needs verification

        /* if ($user->blocked) {
            return response()->json([
                'error' => 'User is blocked',
                'code' => 'blocked',
            ], 401);
        }

        if (!$user->isVerified()) {
            $user->sendVerifyEmail();
            return response()->json([
                'error' => 'User needs verification',
                'code' => 'verification_required',
            ], 401);
        } */

        // Check if the user already has a token
        if ($user->tokens()->count() > 0) {
            // Delete the user's token
            $user->tokens()->delete();
        }

        $token = $user->createToken('authToken', ['*'], now()->addDay())->plainTextToken;

        return response()->json([
            'success' => true,
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
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }


    /**
     * Login for testing (create a user and return the token + user)
     */
    public function testing(Request $request) {
        $data = $request->only('testing_key');

        if (env('APP_ENV') != 'testing') {
            return response()->json([
                'error' => 'Not in testing environment',
                'code' => 'not_testing',
            ], 400);
        }

        if (!isset($data['testing_key']) || empty($data['testing_key'])) {
            return response()->json([
                'error' => 'No testing key provided',
                'code' => 'no_key',
            ], 400);
        }

        // Make sure not everyone can just get an testing token
        if ($data['testing_key'] != 'MKfUKBND9s901CkR2aj5MIagDlM7jXAl') {
            return response()->json([
                'error' => 'Invalid testing key',
                'code' => 'invalid_key',
            ], 400);
        }

        $user = User::where('email', 'testing-user@app.com')->first();
        if (!$user) {
            $user = User::factory()->create(["email"=>"testing-user@app.com"]);
        }

        $token = $user->createToken('authToken', ['*'], now()->addDay())->plainTextToken;

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }


    /**
     * Logout for testing (delete the user for next use)
     */
    public function testing_logout(Request $request) {
        if (!$request->user()) {
            return response()->json([
                'error' => 'No user logged in',
                'code' => 'no_user',
            ], 400);
        }

        $request->user()->tokens()->delete();
        // Delete the user
        $request->user()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }

}
