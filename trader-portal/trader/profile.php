<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/shop-media.php';

$me = require_trader();
$uid = (string) $me['user_id'];
$shopId = (string) $me['shop_id'];

$userRow = db_fetch_one('SELECT * FROM users WHERE user_id = :id', ['id' => $uid]) ?? [];
$shopRow = $shopId !== ''
    ? (db_fetch_one('SELECT * FROM shop WHERE shop_id = :id', ['id' => $shopId]) ?? [])
    : [];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!portal_verify_csrf()) {
        flash_set('error', 'Invalid CSRF token.');
    } else {
        $shopName = trim((string) ($_POST['shop_name'] ?? ''));
        $desc = trim((string) ($_POST['shop_description'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $cutoff = trim((string) ($_POST['order_cutoff'] ?? ''));
        $maxSlot = max(1, (int) ($_POST['max_orders_per_slot'] ?? 20));
        $category = trim((string) ($_POST['trader_category'] ?? ''));

        try {
            $st1 = db_execute(
                'UPDATE users SET phone_num = :p, email = :e WHERE user_id = :user_id',
                ['p' => $phone, 'e' => $email, 'user_id' => $uid]
            );
            if ($st1) {
                oci_free_statement($st1);
            }

            if ($shopId !== '' && $shopId !== '0') {
                $locMeta = 'CAT:' . $category . '|DESC:' . substr($desc, 0, 500)
                    . '|CO:' . $cutoff . '|MAX:' . $maxSlot;
                $st2 = db_execute(
                    'UPDATE shop SET shop_name = :sn, location = :loc WHERE shop_id = :sid',
                    ['sn' => $shopName, 'loc' => $locMeta, 'sid' => $shopId]
                );
                if ($st2) {
                    oci_free_statement($st2);
                }
            }

            if (!empty($_FILES['shop_logo']['tmp_name']) && is_uploaded_file($_FILES['shop_logo']['tmp_name'])) {
                $dir = dirname(__DIR__) . '/assets/uploads/shop/' . $shopId;
                if (!is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }
                $ext = pathinfo($_FILES['shop_logo']['name'], PATHINFO_EXTENSION);
                $target = $dir . '/logo.' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $ext) ?: 'png');
                move_uploaded_file($_FILES['shop_logo']['tmp_name'], $target);
            }

            db_commit();
            flash_set('success', 'Changes saved.');
            auth_refresh_from_db($uid);
        } catch (Throwable $e) {
            db_rollback();
            flash_set('error', 'Could not save: ' . $e->getMessage());
        }
    }
    portal_redirect('/trader/profile.php');
}

$pageTitle = 'Profile';
$traderLayout = true;
$traderPageTitle = 'Shop profile';
$traderPageEyebrow = 'Your account';
$traderPageSubtitle = (string) ($shopRow['shop_name'] ?? 'Update shop and contact details');
$traderPageActionsHtml = '<a class="btn btn-outline" href="' . h(portal_url('trader/settings.php')) . '">Settings</a>';
require_once dirname(__DIR__) . '/includes/header.php';

$flash = flash_get();
?>
    <div class="trader-dashboard">
        <?php require dirname(__DIR__) . '/includes/partials/trader-page-header.php'; ?>

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>"><?= h($flash['message']) ?></div>
        <?php endif; ?>

        <nav class="dash-nav-pills" aria-label="Profile sections">
            <a href="#shop-details">Shop details</a>
            <a href="#photos">Logo</a>
            <a href="#account">Account</a>
        </nav>

        <form method="post" enctype="multipart/form-data" class="dash-panel dash-form">
            <?= portal_csrf_field() ?>

            <h2 class="wf-section-title" id="shop-details">Shop details</h2>

            <section id="photos" style="margin-bottom:24px;">
                <label for="shop_logo">Shop logo</label>
                <?php $currentLogo = $shopId !== '' ? shop_logo_public_url($shopId) : null; ?>
                <?php if ($currentLogo): ?>
                    <p class="shop-logo-preview-wrap">
                        <img src="<?= h($currentLogo) ?>" alt="" class="shop-logo-preview">
                    </p>
                <?php endif; ?>
                <input type="file" name="shop_logo" id="shop_logo" accept="image/*" class="input">
            </section>

            <div class="form-grid cols-2">
                <div style="grid-column:1/-1;">
                    <label for="shop_name">Shop name</label>
                    <input class="input" id="shop_name" name="shop_name" required
                           value="<?= h((string) ($shopRow['shop_name'] ?? '')) ?>">
                </div>
                <div style="grid-column:1/-1;">
                    <label for="trader_category">Trader category</label>
                    <input class="input" id="trader_category" name="trader_category" placeholder="Butcher, Bakery…">
                </div>
                <div style="grid-column:1/-1;">
                    <label for="shop_description">Shop description</label>
                    <textarea class="input" id="shop_description" name="shop_description" rows="4"></textarea>
                </div>
                <div>
                    <label for="phone">Contact phone</label>
                    <input class="input" id="phone" name="phone" value="<?= h((string) ($userRow['phone_num'] ?? '')) ?>">
                </div>
                <div>
                    <label for="email">Email</label>
                    <input class="input" id="email" name="email" type="email" required
                           value="<?= h((string) ($userRow['email'] ?? '')) ?>">
                </div>
                <div>
                    <label for="order_cutoff">Order cut-off time</label>
                    <input class="input" id="order_cutoff" name="order_cutoff" type="time" value="18:00">
                </div>
                <div>
                    <label for="max_orders_per_slot">Max orders per slot</label>
                    <input class="input" type="number" min="1" id="max_orders_per_slot" name="max_orders_per_slot" value="20">
                </div>
            </div>

            <div id="account" class="notice-box" style="margin-top:24px;">
                Password changes: use
                <a href="<?= h(portal_url('forgot-password.php')) ?>">Forgot password</a>
                on the trader login page.
            </div>

            <div class="filter-actions" style="margin-top:24px;">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <a href="<?= h(portal_url('trader/dashboard.php')) ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
