<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\EmailVerification;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

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
        $user = User::create($data);

        // Assign default member role
        $user->assignDefaultRole();

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

        // Reassign roles based on current positions
        $user->assignDefaultRole();
        $user->load('positions.role', 'roles');

        // Generate token
        $token = $user->createToken('site-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'data'    => $user,
            'token'   => $token,
            'token_type' => 'Bearer',
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

        // Reassign roles based on current positions
        $user->assignDefaultRole();

        // Only allow users who have a role with at least one permission
        $hasAdminRole = $user->roles()
            ->where('name', '!=', 'member')
            ->whereHas('permissions')
            ->exists();

        if (!$hasAdminRole) {
            return response()->json([
                'message' => 'Access denied. This area is restricted to board members.'
            ], 403);
        }

        // Create admin-specific token
        $token = $user->createToken('admin-token', ['admin'])->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'data'  => $user->load(['positions', 'roles']),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Admin logout
     */
    public function adminLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * EventsGate visitor register
     */
    public function eventsGateRegister(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:20|unique:users,phone_number',
            'national_id' => 'required|string|max:50|unique:users,national_id',
            'password' => 'required|string|min:8|confirmed', // password_confirmation required
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Store registration data and generate verification code
        $verification = EmailVerification::createVerification($validator->validated());

        // Send verification email with 6-digit code
        try {
            $this->sendVerificationEmail($verification->email, $verification->verification_code);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Email sending failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Registration data received. Please check your email for 6-digit verification code.',
            'email' => $verification->email,
            'verification_sent' => true,
            'expires_at' => $verification->expires_at->toDateTimeString(),
            'expires_in_minutes' => 10
        ], 201);
    }

    /**
     * Send verification email with 6-digit code
     */
    private function sendVerificationEmail($email, $code)
    {
        Mail::send([], [], function ($message) use ($email, $code) {
            $message->to($email)
                ->subject('EventsGate - 6-Digit Verification Code')
                ->text("Your EventsGate verification code is: {$code}\n\nThis code expires in 10 minutes.");
        });
    }

    /**
     * Send password reset email with 6-digit code
     */
    private function sendPasswordResetEmail($email, $code)
    {
        Mail::send([], [], function ($message) use ($email, $code) {
            $message->to($email)
                ->subject('EventsGate - Password Reset Code')
                ->text("Your EventsGate password reset code is: {$code}\n\nThis code expires in 10 minutes.\n\nIf you didn't request this, please ignore this email.");
        });
    }

    /**
     * EventsGate visitor login
     */
    public function eventsGateLogin(Request $request)
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

        // Generate join_code if user doesn't have one yet
        if (!$user->join_code) {
            $user->update(['join_code' => User::generateUniqueJoinCode()]);
        }

        // Reassign roles based on current positions
        $user->assignDefaultRole();
        $user->load('positions.role', 'roles');

        // Generate EventsGate-specific token
        $token = $user->createToken('eventsgate-token')->plainTextToken;

        return response()->json([
            'message' => 'EventsGate login successful',
            'data'    => $user,
            'token'   => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Send password reset verification code
     */
    public function eventsGateSendPasswordResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create password reset verification
        $verification = EmailVerification::createPasswordReset($request->email);

        // Send verification email
        try {
            $this->sendPasswordResetEmail($verification->email, $verification->verification_code);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Password reset email failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Password reset code sent to your email',
            'email' => $verification->email,
            'expires_in_minutes' => 10
        ]);
    }

    /**
     * Verify password reset code and reset password
     */
    public function eventsGateResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'verification_code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify the reset code
        $verification = EmailVerification::verifyPasswordResetCode($request->email, $request->verification_code);

        if (!$verification) {
            return response()->json([
                'message' => 'Invalid or expired verification code',
                'error_code' => 'invalid_code'
            ], 400);
        }

        // Find user and update password
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'error_code' => 'user_not_found'
            ], 404);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete verification record
        $verification->delete();

        return response()->json([
            'message' => 'Password reset successfully. You can now login with your new password.',
        ]);
    }

    /**
     * EventsGate visitor logout
     */
    public function eventsGateLogout(Request $request)
    {
        $user = $request->user();

        // Delete current EventsGate token
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'EventsGate logged out successfully'
        ]);
    }
}
