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
        $error = 'Invalid CSRF token.';
    } else {
        $fn = trim((string) ($_POST['first_name'] ?? ''));
        $ln = trim((string) ($_POST['last_name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $pass = (string) ($_POST['password'] ?? '');
        $shop = trim((string) ($_POST['shop_name'] ?? ''));

        if ($fn === '' || $ln === '' || $email === '' || strlen($pass) < 8 || $shop === '') {
            $error = 'Fill all fields; password min 8 chars.';
        } else {
            try {
                $exists = db_fetch_scalar(
                    'SELECT COUNT(*) FROM "USER" WHERE LOWER(email) = LOWER(:e)',
                    ['e' => $email]
                );
                if ((int) $exists > 0) {
                    $error = 'Email already registered.';
                } else {
                    $hash = password_hash($pass, PASSWORD_DEFAULT);
                    $newUid = (int) db_fetch_scalar('SELECT NVL(MAX(user_id), 0) + 1 FROM "USER"');
                    $newTid = (int) db_fetch_scalar('SELECT NVL(MAX(trader_id), 0) + 1 FROM trader');
                    $newSid = (int) db_fetch_scalar('SELECT NVL(MAX(shop_id), 0) + 1 FROM shop');

                    $st = db_execute(
                        'INSERT INTO "USER" (user_id, first_name, last_name, email, password, role, phone_num, address, created_at)
                         VALUES (:id, :fn, :ln, :em, :pw, \'trader\', \'-\', \'-\', SYSTIMESTAMP)',
                        ['id' => $newUid, 'fn' => $fn, 'ln' => $ln, 'em' => $email, 'pw' => $hash]
                    );
                    if ($st) {
                        oci_free_statement($st);
                    }

                    $st = db_execute(
                        'INSERT INTO trader (trader_id, user_id) VALUES (:tid, :uid)',
                        ['tid' => $newTid, 'uid' => $newUid]
                    );
                    if ($st) {
                        oci_free_statement($st);
                    }

                    $st = db_execute(
                        'INSERT INTO shop (shop_id, shop_name, location, trader_id) VALUES (:sid, :sn, :loc, :tid)',
                        ['sid' => $newSid, 'sn' => $shop, 'loc' => 'Registered via trader portal', 'tid' => $newTid]
                    );
                    if ($st) {
                        oci_free_statement($st);
                    }

                    db_commit();
                    login_trader($email, $pass);
                    portal_redirect('/trader/dashboard.php');
                }
            } catch (Throwable $e) {
                db_rollback();
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

$pageTitle = 'Register trader';
$traderLayout = false;
require_once __DIR__ . '/includes/header.php';
?>
      <article class="card auth-card" style="max-width:520px;margin-left:auto;margin-right:auto;">
        <header class="auth-header">
          <h1>Create trader account</h1>
          <p class="text-secondary">Creates rows in <code>USER</code>, <code>TRADER</code>, and <code>SHOP</code>. Uses MAX(id)+1 — switch to sequences for production.</p>
        </header>
        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>
        <form method="post" class="auth-form">
            <?= portal_csrf_field() ?>
            <div class="field-group">
                <label for="first_name">First name</label>
                <input id="first_name" name="first_name" required value="<?= h($_POST['first_name'] ?? '') ?>">
            </div>
            <div class="field-group">
                <label for="last_name">Last name</label>
                <input id="last_name" name="last_name" required value="<?= h($_POST['last_name'] ?? '') ?>">
            </div>
            <div class="field-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?= h($_POST['email'] ?? '') ?>">
            </div>
            <div class="field-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8">
            </div>
            <div class="field-group">
                <label for="shop_name">Shop name</label>
                <input id="shop_name" name="shop_name" required value="<?= h($_POST['shop_name'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary auth-submit">Register</button>
        </form>
        <p class="text-secondary" style="text-align:center;margin-top:18px;font-size:14px;"><a href="<?= h(portal_url('login.php')) ?>">Back to login</a></p>
      </article>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
