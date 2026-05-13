<?php

declare(strict_types=1);

/**
 * Shared shell with Laravel customer UI (/css/style.css) + trader overrides.
 *
 * Set before include:
 *   $pageTitle (string)
 *   $traderLayout (bool) — true = logged-in trader tools
 *   $wrapId (optional string) — id on .trader-page-wrap (e.g. traderMain for dashboard JS)
 */
$traderLayout = $traderLayout ?? false;
$wrapId = $wrapId ?? '';
$me = auth_user();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= h(portal_csrf_token()) ?>">
    <title><?= h($pageTitle ?? 'GroceryGo Trader') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/trader-portal/assets/css/trader-portal.css">
</head>
<body class="<?= ($traderLayout && $me) ? 'layout-app' : 'layout-auth-page' ?>">
<?php require __DIR__ . '/partials/site-header.php'; ?>

<?php if ($traderLayout && $me): ?>
<?php require __DIR__ . '/partials/trader-strip.php'; ?>
<main>
    <div class="container trader-page-wrap"<?= $wrapId !== '' ? ' id="' . h($wrapId) . '"' : '' ?>">

<?php elseif (!$traderLayout): ?>
<main>
    <section class="section section-light auth-section">
        <div class="container auth-container">

<?php else: ?>
<main>

<?php endif; ?>
