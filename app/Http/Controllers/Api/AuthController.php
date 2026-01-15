<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user (site)
     */
    public function register(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // password_confirmation required
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Create user
        $data = $validator->validated();
        $data['password'] = Hash::make($data['password']);
        $data['position'] = 'user'; // كل اللي بيسجل هنا users عاديين
        $user = User::create($data);

        // Create token
        $token = $user->createToken('site-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'data'    => $user,
            'token'   => $token,
        ], 201);
    }

    /**
     * Login user (site)
     */
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Check credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Generate token
        $token = $user->createToken('site-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'data'    => $user,
            'token'   => $token,
        ]);
    }

    /**
     * Logout user (site)
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // Delete current token
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Admin login
     */
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if user is not a regular user
        if ($user->position === 'user') {
            return response()->json([
                'message' => 'Access denied. This area is restricted to non-user accounts.'
            ], 403);
        }

        // Create admin-specific token
        $token = $user->createToken('admin-token', ['admin'])->plainTextToken;

        return response()->json([
            'message' => ucfirst($user->position) . ' login successful',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    /**
     * Admin logout
     */
    public function adminLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $user = $request->user();
        return response()->json([
            'message' => ucfirst($user->position) . ' logged out successfully'
        ]);
    }
}
