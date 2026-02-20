<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailVerificationController extends Controller
{
    /**
     * Verify 6-digit code and create user account
     */
    public function verifyRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'verification_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find verification record
        $verification = EmailVerification::verifyCode($request->email, $request->verification_code);

        if (!$verification) {
            return response()->json([
                'message' => 'Invalid or expired verification code',
                'error_code' => 'invalid_code'
            ], 400);
        }

        // Create user from verification data
        $user = $verification->createUser();


        // Create EventsGate token
        $token = $user->createToken('eventsgate-token')->plainTextToken;

        return response()->json([
            'message' => 'Email verified successfully. Your account has been created!',
            'data' => $user,
            'token' => $token,
        ], 201);
    }
}
