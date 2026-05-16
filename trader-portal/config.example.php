<?php
/**
 * Copy this file to `config.php` and set your Oracle credentials.
 * Do not commit config.php (add to .gitignore).
 */

declare(strict_types=1);

/** Oracle credentials: edit ../db.php or ../db.local.php at project root. */

/** Laravel customer app URL path (no trailing slash), e.g. '/GG-TP' or '' on :8080 vhost */
define('APP_BASE', '/GG-TP');

/** Trader portal URL path (no trailing slash), e.g. '/GG-TP/trader-portal' */
define('PORTAL_BASE', '/GG-TP/trader-portal');

/** Set true only on local dev to open diagnose.php */
define('PORTAL_DIAGNOSE_ENABLED', true);

/** CSRF & session */
define('SESSION_NAME', 'GG_TRADER_PORTAL');

/** Upload limits */
define('MAX_UPLOAD_MB', 5);
define('ALLOWED_IMAGE_MIME', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
