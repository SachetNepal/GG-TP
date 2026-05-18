<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/password-reset.php';

if (auth_user()) {
    portal_redirect('/trader/dashboard.php');
}

$token = trim((string) ($_GET['token'] ?? $_POST['token'] ?? ''));
$error = '';
$done = false;

$row = $token !== '' ? portal_password_reset_row($token) : null;
if ($token !== '' && $row === null && ($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    $error = 'This reset link is invalid or has expired.';
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && $token !== '') {
    if (!portal_verify_csrf()) {
        $error = 'Invalid security token.';
    } else {
        $pass = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirmation'] ?? '');
        if (strlen($pass) < 8) {
            $error = 'Password must be at least 8 characters.';
        } elseif ($pass !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            $row = portal_password_reset_row($token);
            if (!$row) {
                $error = 'This reset link is invalid or has expired.';
            } else {
                try {
                    portal_complete_password_reset(
                        (string) $row['verification_id'],
                        (string) $row['user_id'],
                        $pass
                    );
                    $done = true;
                } catch (Throwable $e) {
                    db_rollback();
                    $error = 'Could not update password.';
                }
            }
        }
    }
}

$pageTitle = 'Reset password';
$traderLayout = false;
require_once __DIR__ . '/includes/header.php';
?>
      <article class="card auth-card" style="max-width:480px;margin-left:auto;margin-right:auto;">
        <header class="auth-header">
          <h1>Choose a new password</h1>
        </header>
        <?php if ($done): ?>
            <div class="alert" style="background:#ecfdf5;color:#166534;padding:12px;border-radius:8px;">
                Password updated. You can sign in now.
            </div>
            <p style="margin-top:16px;text-align:center;"><a class="btn btn-primary" href="<?= h(portal_url('login.php')) ?>">Sign in</a></p>
        <?php elseif ($error !== '' && $row === null): ?>
            <div class="alert alert-error"><?= h($error) ?></div>
            <p style="margin-top:16px;text-align:center;"><a href="<?= h(portal_url('forgot-password.php')) ?>">Request a new link</a></p>
        <?php else: ?>
            <?php if ($error !== ''): ?>
                <div class="alert alert-error"><?= h($error) ?></div>
            <?php endif; ?>
            <form method="post" class="auth-form">
                <?= portal_csrf_field() ?>
                <input type="hidden" name="token" value="<?= h($token) ?>">
                <div class="field-group">
                    <label for="password">New password</label>
                    <input class="input" type="password" id="password" name="password" required minlength="8">
                </div>
                <div class="field-group">
                    <label for="password_confirmation">Confirm password</label>
                    <input class="input" type="password" id="password_confirmation" name="password_confirmation" required minlength="8">
                </div>
                <button type="submit" class="btn btn-primary auth-submit">Update password</button>
            </form>
        <?php endif; ?>
      </article>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
