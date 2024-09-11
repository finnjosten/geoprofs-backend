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

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function register(Request $request) {

        /**
         * Items to fill with register
         *
         * name - max 64 chars
         * email - just an email
         * password - plain text password min 8 chars max 64 chars
         *
         * optional:
         * role_id - int from 1 to n (n being the last id of the roles)
         * department_id - int from 1 to n (n being the last id of the departments)
        **/

        // Parse the raw JSON data
        $data = $request->all();

        // Define validation rules
        $validator = Validator::make($data, [
            'name' => 'required|string|max:64',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|max:64',
            'role_id' => 'nullable|integer|exists:roles,id',
            'department_id' => 'nullable|integer|exists:departments,id',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return a JSON response with validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create the user with the validated data
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role_id' => $data['role_id'] ?? 1, // Default role ID to 1 if not provided
            'department_id' => $data['department_id'] ?? null,
        ]);


        // Return a success response
        return response()->json([
            'message' => 'User registered successfully!',
            'user' => $user,
        ]);
    }
}
