<?php

declare(strict_types=1);

$traderLayout = $traderLayout ?? false;
$me = auth_user();

if ($traderLayout && $me): ?>
    </div><!-- .trader-page-wrap -->
</main>
<?php elseif (!$traderLayout): ?>
        </div><!-- .auth-container -->
    </section><!-- .auth-section -->
</main>
<?php else: ?>
</main>
<?php endif; ?>

<?php require __DIR__ . '/partials/site-footer.php'; ?>

<div id="toast-root" class="toast-root" aria-live="polite"></div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
<script src="<?= h(portal_asset('js/app.js')) ?>" defer></script>
</body>
</html>
