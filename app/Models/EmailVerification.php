<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'national_id',
        'password',
        'verification_code',
        'expires_at',
        'type', // 'registration' or 'password_reset'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Store registration data and generate 6-digit verification code
     */
    public static function createVerification($data)
    {
        // Delete any existing verification for this email
        self::where('email', $data['email'])->delete();

        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Hash password
        $hashedPassword = \Illuminate\Support\Facades\Hash::make($data['password']);

        // Create verification record
        return self::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'national_id' => $data['national_id'],
            'password' => $hashedPassword,
            'verification_code' => $code,
            'expires_at' => now()->addMinutes(10), // 10 minutes expiry
            'type' => 'registration',
        ]);
    }

    /**
     * Create password reset verification code
     */
    public static function createPasswordReset($email)
    {
        // Delete any existing password reset for this email
        self::where('email', $email)->where('type', 'password_reset')->delete();

        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Create password reset record
        return self::create([
            'email' => $email,
            'verification_code' => $code,
            'expires_at' => now()->addMinutes(10), // 10 minutes expiry
            'type' => 'password_reset',
        ]);
    }

    /**
     * Verify password reset code
     */
    public static function verifyPasswordResetCode($email, $code)
    {
        return self::where('email', $email)
            ->where('verification_code', $code)
            ->where('type', 'password_reset')
            ->where('expires_at', '>', now())
            ->first();
    }

    /**
     * Verify 6-digit code for registration
     */
    public static function verifyCode($email, $code)
    {
        return self::where('email', $email)
            ->where('verification_code', $code)
            ->where('type', 'registration')
            ->where('expires_at', '>', now())
            ->first();
    }

    /**
     * Create user from verification data
     */
    public function createUser()
    {
        $user = \App\Models\User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'national_id' => $this->national_id,
            'password' => $this->password,
            'email_verified_at' => now(),
        ]);

        // Delete verification record after user creation
        $this->delete();

        return $user;
    }
}
