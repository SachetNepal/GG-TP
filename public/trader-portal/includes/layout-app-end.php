<?php

declare(strict_types=1);

$chartJs = $chartJs ?? false;
$inlineScript = $inlineScript ?? '';

?>
  </div>
</main>
<?php require dirname(__DIR__) . '/includes/partials/site-footer.php'; ?>
<?php if ($chartJs): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<?php endif; ?>
<script src="assets/js/trader-portal.js"></script>
<?php if ($inlineScript !== ''): ?>
<?= $inlineScript ?>
<?php endif; ?>
</body>
</html>
