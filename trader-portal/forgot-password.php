<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/password-reset.php';

if (auth_user()) {
    portal_redirect('/trader/dashboard.php');
}

$error = '';
$sent = false;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!portal_verify_csrf()) {
        $error = 'Invalid security token.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        if ($email === '') {
            $error = 'Enter your email address.';
        } else {
            try {
                $trader = portal_trader_by_email($email);
                if ($trader) {
                    $token = portal_create_password_reset((string) $trader['user_id'], $email);
                    portal_send_password_reset_email($trader, $token);
                }
                $sent = true;
            } catch (Throwable $e) {
                error_log('forgot-password: ' . $e->getMessage());
                $error = 'Could not send reset email. Check mail configuration.';
            }
        }
    }
}

$pageTitle = 'Forgot password';
$traderLayout = false;
require_once __DIR__ . '/includes/header.php';
?>
      <article class="card auth-card" style="max-width:480px;margin-left:auto;margin-right:auto;">
        <header class="auth-header">
          <h1>Reset password</h1>
          <p class="text-secondary">We will email you a link if this address is registered as a trader.</p>
        </header>
        <?php if ($sent): ?>
            <div class="alert" style="background:#ecfdf5;color:#166534;padding:12px;border-radius:8px;">
                If an account exists for that email, a reset link has been sent.
            </div>
        <?php elseif ($error !== ''): ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>
        <?php if (!$sent): ?>
        <form method="post" class="auth-form">
            <?= portal_csrf_field() ?>
            <div class="field-group">
                <label for="email">Email</label>
                <input class="input" type="email" id="email" name="email" required value="<?= h($_POST['email'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary auth-submit">Send reset link</button>
        </form>
        <?php endif; ?>
        <p style="margin-top:16px;text-align:center;"><a href="<?= h(portal_url('login.php')) ?>">Back to login</a></p>
      </article>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
