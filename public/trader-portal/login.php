<?php

declare(strict_types=1);

$pageTitle = 'Trader sign in — GroceryGo';
require __DIR__ . '/includes/layout-auth-start.php';
?>
      <article class="card auth-card">
        <header class="auth-header">
          <h1>Trader sign in</h1>
          <p>Demo preview — Continue opens the trader dashboard.</p>
        </header>
        <form class="auth-form" action="dashboard.php" method="get">
          <div class="field-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" autocomplete="username" placeholder="you@example.com" required>
          </div>
          <div class="field-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password" placeholder="Enter your password" required>
          </div>
          <button type="submit" class="btn btn-primary auth-submit">Continue</button>
        </form>
        <div class="auth-divider"><span>New trader?</span></div>
        <a href="register.php" class="btn auth-secondary-btn">Create trader account</a>
        <p class="text-secondary" style="text-align:center;margin-top:18px;font-size:14px;"><a href="index.php">← Trader portal hub</a></p>
      </article>
<?php
require __DIR__ . '/includes/layout-auth-end.php';
