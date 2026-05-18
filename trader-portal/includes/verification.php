<?php
/**
 * Trader portal email verification (reuses Laravel EmailVerificationService + Oracle).
 */
declare(strict_types=1);

require_once __DIR__ . '/portal-laravel.php';

function portal_user_email_verified(string $userId): bool
{
    try {
        $row = db_fetch_one(
            'SELECT email_verified FROM users WHERE user_id = :id',
            ['id' => $userId]
        );
        if ($row !== null && isset($row['email_verified'])) {
            return (int) ($row['email_verified'] ?? 0) === 1;
        }
    } catch (Throwable) {
        // column may be missing on old schemas
    }

    $n = (int) (db_fetch_scalar(
        "SELECT COUNT(*) FROM verification
         WHERE user_id = :id AND is_verified = 1",
        ['id' => $userId]
    ) ?? 0);

    return $n > 0;
}

function portal_send_signup_verification(string $userId): void
{
    portal_laravel_app();
    $user = \App\Models\User::query()->findOrFail($userId);
    app(\App\Services\Auth\EmailVerificationService::class)->sendSignupCode($user);
}

function portal_verify_signup_code(string $email, string $code): void
{
    portal_laravel_app();
    app(\App\Services\Auth\EmailVerificationService::class)->verifySignupCode($email, $code);
}

function portal_resend_signup_code(string $email): void
{
    portal_laravel_app();
    app(\App\Services\Auth\EmailVerificationService::class)->resendSignupCode($email);
}
