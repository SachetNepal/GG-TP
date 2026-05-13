<?php
/**
 * Open this file in the browser to see why the portal cannot connect.
 * Delete or protect in production.
 */
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Trader portal diagnostics</title>';
echo '<style>body{font-family:system-ui,max-width:720px;margin:24px;} .ok{color:#065f46;} .bad{color:#991b1b;} code{background:#f3f4f6;padding:2px 6px;}</style></head><body>';
echo '<h1>Trader portal diagnostics</h1>';

echo '<h2>1. PHP</h2>';
echo '<p>Version: <code>' . PHP_VERSION . '</code></p>';

$oci = extension_loaded('oci8');
echo '<p>OCI8 extension: ' . ($oci ? '<span class="ok">loaded</span>' : '<span class="bad">NOT loaded</span> — edit <code>php.ini</code> and enable <code>extension=oci8_12c</code> (or your DLL), restart Apache.</p>');

echo '<h2>2. Config file</h2>';
$configPath = __DIR__ . '/config.php';
if (!is_readable($configPath)) {
    echo '<p class="bad">Missing <code>config.php</code>. Copy <code>config.example.php</code> to <code>config.php</code>.</p>';
    echo '</body></html>';
    exit;
}
echo '<p class="ok"><code>config.php</code> found.</p>';
require_once $configPath;

echo '<h2>3. Oracle connection test</h2>';
echo '<p>DSN in use: <code>' . htmlspecialchars((string) ORACLE_DSN) . '</code></p>';
echo '<p>User: <code>' . htmlspecialchars((string) ORACLE_USER) . '</code></p>';

if (!$oci) {
    echo '<p class="bad">Skipping live connect — OCI8 not available.</p>';
    echo '</body></html>';
    exit;
}

$c = @oci_connect(ORACLE_USER, ORACLE_PASS, ORACLE_DSN, 'AL32UTF8');
if ($c) {
    echo '<p class="ok"><strong>Connection succeeded.</strong></p>';
    oci_close($c);
} else {
    $e = oci_error();
    $msg = $e['message'] ?? 'Unknown error';
    echo '<p class="bad"><strong>Connection failed.</strong></p>';
    echo '<pre style="background:#fef2f2;padding:12px;border-radius:8px;">' . htmlspecialchars($msg) . '</pre>';
    echo '<p><strong>Typical fixes:</strong></p><ul>';
    echo '<li>Oracle DB / listener running (Windows service <code>OracleServiceXE</code>, <code>TNS Listener</code>).</li>';
    echo '<li>Correct service name in DSN, e.g. <code>localhost/XEPDB1</code> or <code>localhost/orclpdb1</code>.</li>';
    echo '<li>Oracle Instant Client matches PHP OCI8 (64-bit vs 32-bit).</li>';
    echo '<li>Firewall allowing port 1521.</li>';
    echo '</ul>';
}

echo '<h2>4. URL / PORTAL_BASE</h2>';
echo '<p>If links go to the wrong place, set <code>PORTAL_BASE</code> in <code>config.php</code>:</p>';
echo '<ul>';
echo '<li>PHP built-in server from <code>trader-portal</code> folder → use <code>PORTAL_BASE</code> = <code>\'\'</code> (empty).</li>';
echo '<li>Apache URL like <code>http://localhost/trader-portal/</code> → use <code>\'/trader-portal\'</code>.</li>';
echo '</ul>';
echo '<p>Current <code>PORTAL_BASE</code>: <code>' . htmlspecialchars((string) PORTAL_BASE) . '</code></p>';

echo '</body></html>';
