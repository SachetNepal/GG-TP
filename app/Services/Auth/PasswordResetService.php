<?php

namespace App\Services\Auth;

use App\Mail\CustomerPasswordResetMail;
use App\Models\User;
use App\Models\Verification;
use App\Support\OracleId;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class PasswordResetService
{
    public const PURPOSE = 'password_reset';

    public function sendResetLink(string $email): void
    {
        $email = strtolower(trim($email));
        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereHas('customer')
            ->first();

        if (!$user) {
            return;
        }

        Verification::query()
            ->where('user_id', $user->user_id)
            ->where('purpose', self::PURPOSE)
            ->where('status', EmailVerificationService::STATUS_PENDING)
            ->update(['status' => EmailVerificationService::STATUS_EXPIRED]);

        $token = bin2hex(random_bytes(32));

        Verification::create([
            'verification_id' => OracleId::next('VERIFICATION', 'verification_id', 'V'),
            'verification_token' => $token,
            'verification_code' => null,
            'email' => $email,
            'purpose' => self::PURPOSE,
            'status' => EmailVerificationService::STATUS_PENDING,
            'created_at' => now(),
            'expires_at' => now()->addHour(),
            'is_verified' => false,
            'user_id' => $user->user_id,
        ]);

        $url = url('/reset-password?token='.urlencode($token));
        $name = trim($user->first_name.' '.$user->last_name) ?: 'Customer';

        Mail::to($user->email)->send(new CustomerPasswordResetMail($name, $url, now()->addHour()));
    }

    public function resetPassword(string $token, string $password): void
    {
        $verification = Verification::query()
            ->where('verification_token', $token)
            ->where('purpose', self::PURPOSE)
            ->where('status', EmailVerificationService::STATUS_PENDING)
            ->first();

        if (!$verification) {
            throw new RuntimeException('This reset link is invalid or has expired.');
        }

        if ($verification->expires_at && $verification->expires_at->isPast()) {
            $verification->status = EmailVerificationService::STATUS_EXPIRED;
            $verification->save();
            throw new RuntimeException('This reset link has expired. Please request a new one.');
        }

        $user = User::query()->find($verification->user_id);
        if (!$user) {
            throw new RuntimeException('Account not found.');
        }

        DB::connection('oracle')->transaction(function () use ($user, $verification, $password): void {
            $user->password = Hash::make($password);
            $user->save();

            $verification->status = EmailVerificationService::STATUS_USED;
            $verification->is_verified = true;
            $verification->save();
        });
    }
}
