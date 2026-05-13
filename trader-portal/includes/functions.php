<?php
/**
 * Sanitization, CSRF, flash messages, redirects.
 */
declare(strict_types=1);

function h(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function portal_csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function portal_csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . h(portal_csrf_token()) . '">';
}

function portal_verify_csrf(): bool
{
    $t = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return is_string($t) && hash_equals($_SESSION['_csrf'] ?? '', $t);
}

function flash_set(string $type, string $message): void
{
    $_SESSION['_flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    if (empty($_SESSION['_flash'])) {
        return null;
    }
    $f = $_SESSION['_flash'];
    unset($_SESSION['_flash']);
    return $f;
}

function portal_redirect(string $path): never
{
    $base = rtrim(PORTAL_BASE, '/');
    $url = ($base !== '' ? $base : '') . $path;
    header('Location: ' . $url);
    exit;
}

/** Resolve asset URL from portal root */
function portal_asset(string $path): string
{
    $base = rtrim(PORTAL_BASE, '/');
    return ($base !== '' ? $base : '') . '/assets/' . ltrim($path, '/');
}

/** Page URL from portal root */
function portal_url(string $path): string
{
    $base = rtrim(PORTAL_BASE, '/');
    return ($base !== '' ? $base : '') . '/' . ltrim($path, '/');
}

function json_response(array $data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_THROW_ON_ERROR);
    exit;
}

/**
 * Basic filename sanitization for uploads.
 */
function safe_filename(string $name): string
{
    $name = basename($name);
    return preg_replace('/[^a-zA-Z0-9._-]/', '_', $name) ?? 'file';
}
