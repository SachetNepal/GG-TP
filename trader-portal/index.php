<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/auth.php';

if (auth_user()) {
    portal_redirect('/trader/dashboard.php');
}
portal_redirect('/login.php');
