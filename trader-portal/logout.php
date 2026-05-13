<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/auth.php';

logout();
header('Location: ' . rtrim(PORTAL_BASE, '/') . '/login.php');
exit;
