<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$pageTitle = $pageTitle ?? 'GroceryGo Trader';
$mainId = $mainId ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= tp_h($pageTitle) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="assets/css/trader-portal.css">
</head>
<body class="layout-app">
<?php require dirname(__DIR__) . '/includes/partials/site-header.php'; ?>
<?php require dirname(__DIR__) . '/includes/partials/trader-strip.php'; ?>
<main>
  <div class="container trader-page-wrap"<?= $mainId !== '' ? ' id="' . tp_h($mainId) . '"' : '' ?>>
