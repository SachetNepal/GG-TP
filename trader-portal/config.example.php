<?php
/**
 * Copy this file to `config.php` and set your Oracle credentials.
 * Do not commit config.php (add to .gitignore).
 */

declare(strict_types=1);

define('ORACLE_USER', 'your_username');
define('ORACLE_PASS', 'your_password');
define('ORACLE_DSN', 'localhost/XEPDB1'); // host/service name for oci_connect

/** Public URL path to this portal (no trailing slash), e.g. '/trader-portal' or '' */
define('PORTAL_BASE', '/trader-portal');

/** CSRF & session */
define('SESSION_NAME', 'GG_TRADER_PORTAL');

/** Upload limits */
define('MAX_UPLOAD_MB', 5);
define('ALLOWED_IMAGE_MIME', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
