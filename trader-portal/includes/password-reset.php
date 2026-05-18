<?php
/**
 * Trader password reset via VERIFICATION table + Laravel mail.
 */
declare(strict_types=1);

require_once __DIR__ . '/portal-laravel.php';

const PORTAL_RESET_PURPOSE = 'password_reset';
const PORTAL_RESET_STATUS_PENDING = 'pending';
const PORTAL_RESET_STATUS_USED = 'used';

function portal_trader_by_email(string $email): ?array
{
    return db_fetch_one(
        'SELECT u.user_id, u.first_name, u.last_name, u.email
         FROM users u
         INNER JOIN trader t ON t.trader_id = u.user_id
         WHERE LOWER(u.email) = LOWER(:email)',
        ['email' => $email]
    );
}

function portal_create_password_reset(string $userId, string $email): string
{
    db_execute(
        "UPDATE verification SET status = 'expired'
         WHERE user_id = :uid AND purpose = :p AND status = 'pending'",
        ['uid' => $userId, 'p' => PORTAL_RESET_PURPOSE]
    );

    $token = bin2hex(random_bytes(32));
    $vid = db_next_prefixed_id('verification', 'verification_id', 'V');

    db_execute(
        'INSERT INTO verification (
            verification_id, verification_token, verification_code,
            email, purpose, status, created_at, expires_at, is_verified, user_id
        ) VALUES (
            :vid, :tok, NULL, :em, :p, :st, SYSTIMESTAMP,
            SYSTIMESTAMP + INTERVAL \'1\' HOUR, 0, :uid
        )',
        [
            'vid' => $vid,
            'tok' => $token,
            'em' => strtolower($email),
            'p' => PORTAL_RESET_PURPOSE,
            'st' => PORTAL_RESET_STATUS_PENDING,
            'uid' => $userId,
        ]
    );
    db_commit();

    return $token;
}

function portal_send_password_reset_email(array $trader, string $token): void
{
    portal_laravel_app();
    $url = portal_url('reset-password.php?token=' . rawurlencode($token));
    $name = trim((string) ($trader['first_name'] ?? '') . ' ' . (string) ($trader['last_name'] ?? ''));
    if ($name === '') {
        $name = 'Trader';
    }

    \Illuminate\Support\Facades\Mail::to((string) $trader['email'])->send(
        new \App\Mail\TraderPasswordResetMail($name, $url, new \DateTimeImmutable('+1 hour'))
    );
}

/**
 * @return array<string, mixed>|null
 */
function portal_password_reset_row(string $token): ?array
{
    return db_fetch_one(
        "SELECT verification_id, user_id, email, status, expires_at
         FROM verification
         WHERE verification_token = :tok
         AND purpose = :p
         AND status = 'pending'",
        ['tok' => $token, 'p' => PORTAL_RESET_PURPOSE]
    );
}

function portal_complete_password_reset(string $verificationId, string $userId, string $newPassword): void
{
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    db_execute(
        'UPDATE users SET password = :pw WHERE user_id = :uid',
        ['pw' => $hash, 'uid' => $userId]
    );
    db_execute(
        "UPDATE verification SET status = :st, is_verified = 1
         WHERE verification_id = :vid",
        ['st' => PORTAL_RESET_STATUS_USED, 'vid' => $verificationId]
    );
    db_commit();
}
