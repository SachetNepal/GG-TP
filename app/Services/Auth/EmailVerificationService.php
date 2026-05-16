<?php

namespace App\Services\Auth;

use App\Mail\VerifyEmailOtpMail;
use App\Models\User;
use App\Models\Verification;
use App\Support\OracleId;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class EmailVerificationService
{
    public const PURPOSE_SIGNUP = 'signup';
    public const STATUS_PENDING = 'pending';
    public const STATUS_USED = 'used';
    public const STATUS_EXPIRED = 'expired';

    public function sendSignupCode(User $user): Verification
    {
        $verification = $this->createSignupVerification($user);
        $this->dispatchVerificationEmail($user, $verification);

        return $verification;
    }

    public function resendSignupCode(string $email): Verification
    {
        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [strtolower(trim($email))])
            ->firstOrFail();

        if ($this->isUserEmailVerified($user)) {
            throw new RuntimeException('This email is already verified.');
        }

        $this->expirePendingCodes($user->user_id, $user->email, self::PURPOSE_SIGNUP);

        $verification = $this->createSignupVerification($user);
        $this->dispatchVerificationEmail($user, $verification);

        return $verification;
    }

    public function verifySignupCode(string $email, string $code): User
    {
        $email = strtolower(trim($email));
        $code = preg_replace('/\D/', '', $code) ?? '';

        if (strlen($code) !== 6) {
            throw ValidationException::withMessages([
                'code' => 'Please enter a valid 6-digit verification code.',
            ]);
        }

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'No account found for this email.',
            ]);
        }

        if ($this->isUserEmailVerified($user)) {
            return $user;
        }

        $verification = Verification::query()
            ->where('user_id', $user->user_id)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->where('purpose', self::PURPOSE_SIGNUP)
            ->where('status', self::STATUS_PENDING)
            ->where('verification_code', $code)
            ->orderByDesc('created_at')
            ->first();

        if (!$verification) {
            throw ValidationException::withMessages([
                'code' => 'Invalid verification code.',
            ]);
        }

        if ($verification->expires_at && $verification->expires_at->isPast()) {
            $verification->status = self::STATUS_EXPIRED;
            $verification->save();
            throw ValidationException::withMessages([
                'code' => 'Verification code has expired. Please request a new code.',
            ]);
        }

        return DB::connection('oracle')->transaction(function () use ($user, $verification) {
            $verification->status = self::STATUS_USED;
            $verification->is_verified = true;
            $verification->save();

            $user->email_verified = true;
            $user->email_verified_at = now();
            $user->save();

            return $user->fresh();
        });
    }

    public function isUserEmailVerified(User $user): bool
    {
        if (isset($user->email_verified)) {
            return (bool) $user->email_verified;
        }

        return Verification::query()
            ->where('user_id', $user->user_id)
            ->where('is_verified', true)
            ->exists();
    }

    protected function createSignupVerification(User $user): Verification
    {
        $code = $this->generateOtp();

        return Verification::create([
            'verification_id' => OracleId::next('VERIFICATION', 'verification_id', 'V'),
            'verification_token' => Str::uuid()->toString(),
            'verification_code' => $code,
            'email' => strtolower($user->email),
            'purpose' => self::PURPOSE_SIGNUP,
            'status' => self::STATUS_PENDING,
            'created_at' => now(),
            'expires_at' => now()->addMinutes(10),
            'is_verified' => false,
            'user_id' => $user->user_id,
        ]);
    }

    protected function expirePendingCodes(string $userId, string $email, string $purpose): void
    {
        Verification::query()
            ->where('user_id', $userId)
            ->whereRaw('LOWER(email) = ?', [strtolower($email)])
            ->where('purpose', $purpose)
            ->where('status', self::STATUS_PENDING)
            ->update(['status' => self::STATUS_EXPIRED]);
    }

    protected function generateOtp(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    protected function dispatchVerificationEmail(User $user, Verification $verification): void
    {
        try {
            Mail::to($user->email)->send(new VerifyEmailOtpMail(
                $user,
                (string) $verification->verification_code,
                $verification->expires_at,
            ));
        } catch (\Throwable $e) {
            Log::error('Failed to send verification email', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
            throw new RuntimeException('Could not send verification email. Check mail configuration.');
        }
    }
}
