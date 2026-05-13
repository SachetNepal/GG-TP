<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';

require_trader();

$pageTitle = 'Settings';
$traderLayout = true;
require_once dirname(__DIR__) . '/includes/header.php';
?>
    <section class="panel">
        <h1 class="panel-title">Additional settings</h1>
        <p class="muted">Extend this page to map opening hours, payment gateways, and notifications to your Oracle tables (e.g. <code>COLLECTION_SLOT</code>, <code>PAYMENT</code>).</p>
        <p><a href="<?= h(portal_url('trader/profile.php')) ?>">← Back to profile</a></p>
    </section>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
