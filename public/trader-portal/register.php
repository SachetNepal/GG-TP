<?php

declare(strict_types=1);

$pageTitle = 'Trader register — GroceryGo';
require __DIR__ . '/includes/layout-auth-start.php';
?>
      <article class="card auth-card">
        <header class="auth-header">
          <h1>Create trader account</h1>
          <p>Placeholder fields for layout — connect to your backend later.</p>
        </header>
        <form class="auth-form" action="dashboard.php" method="get">
          <div class="field-group">
            <label for="shop">Shop name</label>
            <input type="text" id="shop" name="shop" placeholder="Your stall or shop">
          </div>
          <div class="field-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="you@example.com" required>
          </div>
          <div class="field-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a password" required>
          </div>
          <button type="submit" class="btn btn-primary auth-submit">Register</button>
        </form>
        <p class="text-secondary" style="text-align:center;margin-top:18px;font-size:14px;"><a href="login.php">Back to sign in</a> · <a href="index.php">Hub</a></p>
      </article>
<?php
require __DIR__ . '/includes/layout-auth-end.php';
