<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Send / resend email verification notification.
     * Requires authentication (auth:sanctum).
     */
    public function sendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
            ], 200);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification email sent.',
        ]);
    }

    /**
     * Verify the user's email via the signed URL link in the email.
     * No auth required — the signed URL itself is the security mechanism.
     */
    public function verify(Request $request, $id, $hash)
    {
        // Find the user
        $user = User::findOrFail($id);

        // Validate the hash matches the user's email
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                'message' => 'Invalid verification link.',
            ], 403);
        }

        // Validate the URL signature (tamper-proof + expiry)
        if (!$request->hasValidSignature()) {
            return response()->json([
                'message' => 'Verification link has expired or is invalid.',
            ], 403);
        }

        // Already verified
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
            ], 200);
        }

        // Mark email as verified
        $user->markEmailAsVerified();

        event(new Verified($user));

        return response()->json([
            'message' => 'Email verified successfully.',
            'data'    => $user,
        ]);
    }
}
