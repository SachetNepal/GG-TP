<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/auth.php';

if (auth_user()) {
    portal_redirect('/trader/dashboard.php');
}

$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!portal_verify_csrf()) {
        $error = 'Invalid security token. Refresh the page.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if ($email === '' || $password === '') {
            $error = 'Enter email and password.';
        } else {
            try {
                $ok = login_trader($email, $password);
            } catch (Throwable $e) {
                $error = 'Database error — Oracle may be down or misconfigured. Open diagnose.php for details.';
                error_log('login_trader: ' . $e->getMessage());
                $ok = false;
            }
            if (!empty($ok)) {
                portal_redirect('/trader/dashboard.php');
            }
            if ($error === '') {
                $error = 'Invalid credentials or not a trader account.';
            }
        }
    }
}

$pageTitle = 'Trader Login';
$traderLayout = false;
require_once __DIR__ . '/includes/header.php';
?>
      <article class="card auth-card">
        <header class="auth-header">
          <h1>Trader sign in</h1>
          <p class="text-secondary">Oracle-backed session · role must be <code>trader</code></p>
        </header>
        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>
        <form method="post" class="auth-form" autocomplete="on">
            <?= portal_csrf_field() ?>
            <div class="field-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?= h($_POST['email'] ?? '') ?>" placeholder="you@example.com">
            </div>
            <div class="field-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
            <button type="submit" class="btn btn-primary auth-submit">Continue</button>
        </form>
        <div class="auth-divider"><span>New trader?</span></div>
        <a href="<?= h(portal_url('register.php')) ?>" class="btn auth-secondary-btn">Create trader account</a>
      </article>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
