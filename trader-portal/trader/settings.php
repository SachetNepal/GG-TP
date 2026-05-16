<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$me = require_trader();
$shopId = trader_shop_id($me);
$message = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && portal_verify_csrf()) {
    $notify = trim((string) ($_POST['notify_email'] ?? ''));
    if ($shopId > 0 && $notify !== '') {
        try {
            $st = db_execute(
                'UPDATE shop SET location = :loc WHERE shop_id = :sid',
                ['loc' => 'NOTIFY:' . $notify, 'sid' => $shopId]
            );
            if ($st) {
                oci_free_statement($st);
            }
            db_commit();
            flash_set('success', 'Notification email saved.');
            portal_redirect('/trader/settings.php');
        } catch (Throwable $e) {
            db_rollback();
            $message = $e->getMessage();
        }
    }
}

$shop = $shopId > 0
    ? (db_fetch_one('SELECT shop_name, location FROM shop WHERE shop_id = :id', ['id' => $shopId]) ?? [])
    : [];
$notifyEmail = '';
if (!empty($shop['location']) && str_starts_with((string) $shop['location'], 'NOTIFY:')) {
    $notifyEmail = substr((string) $shop['location'], 7);
}

$pageTitle = 'Settings';
$traderLayout = true;
require_once dirname(__DIR__) . '/includes/header.php';
$flash = flash_get();
?>
    <section class="panel">
        <h1 class="panel-title">Settings</h1>
        <?php if ($flash): ?>
            <p class="<?= ($flash['type'] ?? '') === 'error' ? 'bad' : 'ok' ?>"><?= h((string) ($flash['message'] ?? '')) ?></p>
        <?php endif; ?>
        <?php if ($message !== ''): ?>
            <p class="bad"><?= h($message) ?></p>
        <?php endif; ?>
        <form method="post" class="form-grid cols-2" style="max-width:520px;margin-top:16px;">
            <?= portal_csrf_field() ?>
            <div style="grid-column:1/-1;">
                <label for="notify_email">Order notification email</label>
                <input class="input" type="email" id="notify_email" name="notify_email"
                       value="<?= h($notifyEmail !== '' ? $notifyEmail : (string) ($me['email'] ?? '')) ?>">
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
        <p style="margin-top:24px;"><a href="<?= h(portal_url('trader/profile.php')) ?>">← Back to profile</a></p>
    </section>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
