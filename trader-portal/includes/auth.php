<?php
/**
 * Session auth: trader role via USER + TRADER join.
 */
declare(strict_types=1);

function auth_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    return [
        'user_id' => (int) $_SESSION['user_id'],
        'trader_id' => (int) ($_SESSION['trader_id'] ?? 0),
        'shop_id' => (int) ($_SESSION['shop_id'] ?? 0),
        'email' => (string) ($_SESSION['email'] ?? ''),
        'display_name' => (string) ($_SESSION['display_name'] ?? 'Trader'),
        'role' => (string) ($_SESSION['role'] ?? ''),
    ];
}

function require_trader(): array
{
    $u = auth_user();
    $roleOk = $u && strtolower($u['role']) === 'trader';
    if (!$u || $u['trader_id'] < 1 || !$roleOk) {
        flash_set('error', 'Please sign in as a trader.');
        portal_redirect('/login.php');
    }
    return $u;
}

/**
 * Load trader context from DB into session (shop_id, names).
 */
function auth_refresh_from_db(int $userId): void
{
    $sql = 'SELECT u.user_id, u.first_name, u.last_name, u.email, u.role,
                   t.trader_id, s.shop_id, s.shop_name
            FROM "USER" u
            INNER JOIN TRADER t ON t.user_id = u.user_id
            LEFT JOIN SHOP s ON s.trader_id = t.trader_id
            WHERE u.user_id = :uid';
    try {
        $row = db_fetch_one($sql, ['uid' => $userId]);
    } catch (Throwable $e) {
        return;
    }
    if (!$row) {
        return;
    }
    $_SESSION['user_id'] = (int) $row['user_id'];
    $_SESSION['trader_id'] = (int) $row['trader_id'];
    $_SESSION['shop_id'] = (int) ($row['shop_id'] ?? 0);
    $_SESSION['email'] = (string) $row['email'];
    $_SESSION['role'] = (string) $row['role'];
    $fn = trim((string) ($row['first_name'] ?? '') . ' ' . (string) ($row['last_name'] ?? ''));
    $_SESSION['display_name'] = $fn !== '' ? $fn : (string) ($row['shop_name'] ?? 'Trader');
}

function login_trader(string $email, string $password): bool
{
    $sql = 'SELECT user_id, email, password, role, first_name, last_name
            FROM "USER" WHERE LOWER(email) = LOWER(:email)';
    try {
        $row = db_fetch_one($sql, ['email' => $email]);
    } catch (Throwable $e) {
        return false;
    }
    if (!$row || strtolower((string) $row['role']) !== 'trader') {
        return false;
    }
    $hash = (string) $row['password'];
    if (!password_verify($password, $hash)) {
        return false;
    }
    $_SESSION['user_id'] = (int) $row['user_id'];
    $_SESSION['email'] = (string) $row['email'];
    $_SESSION['role'] = (string) $row['role'];
    auth_refresh_from_db((int) $row['user_id']);
    return true;
}

function logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
