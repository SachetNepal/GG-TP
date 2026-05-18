<?php
/**
 * Shared page header for trader tools (matches dashboard).
 *
 * @var string $traderPageTitle
 * @var string $traderPageEyebrow
 * @var string $traderPageSubtitle
 * @var string $traderPageActionsHtml
 */
declare(strict_types=1);

$traderPageEyebrow = $traderPageEyebrow ?? 'Trader portal';
$traderPageSubtitle = $traderPageSubtitle ?? '';
$traderPageActionsHtml = $traderPageActionsHtml ?? '';
?>
<header class="dash-header">
    <div>
        <p class="dash-eyebrow"><?= h($traderPageEyebrow) ?></p>
        <h1 class="dash-title"><?= h($traderPageTitle ?? 'Page') ?></h1>
        <?php if ($traderPageSubtitle !== ''): ?>
            <p class="dash-subtitle"><?= h($traderPageSubtitle) ?></p>
        <?php endif; ?>
    </div>
    <?php if ($traderPageActionsHtml !== ''): ?>
        <div class="dash-header-actions"><?= $traderPageActionsHtml ?></div>
    <?php endif; ?>
</header>
