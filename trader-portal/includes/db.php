<?php
/**
 * Oracle OCI8 connection and safe statement helpers.
 */
declare(strict_types=1);

/**
 * @return resource|false Oracle connection
 */
function db_conn()
{
    static $conn = null;
    if ($conn !== null) {
        return $conn;
    }
    if (!function_exists('oci_connect')) {
        throw new RuntimeException('PHP OCI8 extension is not enabled.');
    }
    $conn = @oci_connect(ORACLE_USER, ORACLE_PASS, ORACLE_DSN, 'AL32UTF8');
    if (!$conn) {
        $e = oci_error();
        throw new RuntimeException('Oracle connection failed: ' . ($e['message'] ?? 'unknown'));
    }
    return $conn;
}

/**
 * Oracle returns uppercase keys; normalize to lowercase for PHP.
 *
 * @param array<string, mixed> $row
 * @return array<string, mixed>
 */
function db_normalize_row(array $row): array
{
    $out = [];
    foreach ($row as $k => $v) {
        $out[strtolower((string) $k)] = $v;
    }
    return $out;
}

/**
 * Execute SQL with named binds (OCI requires bind variables by reference).
 *
 * @param array<string, mixed> $binds
 * @return resource|false
 */
function db_execute(string $sql, array $binds = [])
{
    $conn = db_conn();
    $st = oci_parse($conn, $sql);
    if (!$st) {
        return false;
    }
    // Copy binds into a local array so we can bind by reference
    $vars = [];
    foreach ($binds as $name => $value) {
        $vars[ltrim((string) $name, ':')] = $value;
    }
    foreach ($vars as $key => &$val) {
        oci_bind_by_name($st, ':' . $key, $val);
    }
    unset($val);

    if (!oci_execute($st, OCI_NO_AUTO_COMMIT)) {
        return false;
    }
    return $st;
}

/**
 * @return list<array<string, mixed>>
 */
function db_fetch_all(string $sql, array $binds = []): array
{
    $st = db_execute($sql, $binds);
    if (!$st) {
        $e = oci_error();
        throw new RuntimeException($e['message'] ?? 'Query failed');
    }
    oci_fetch_all($st, $rows, 0, -1, OCI_FETCHSTATEMENT_BY_ROW | OCI_ASSOC);
    oci_free_statement($st);
    if (!$rows) {
        return [];
    }
    return array_map('db_normalize_row', $rows);
}

/**
 * @return array<string, mixed>|null
 */
function db_fetch_one(string $sql, array $binds = []): ?array
{
    $rows = db_fetch_all($sql, $binds);
    return $rows[0] ?? null;
}

/**
 * @return mixed|null single scalar first column
 */
function db_fetch_scalar(string $sql, array $binds = [])
{
    $row = db_fetch_one($sql, $binds);
    if ($row === null) {
        return null;
    }
    return reset($row);
}

function db_commit(): bool
{
    return oci_commit(db_conn());
}

function db_rollback(): bool
{
    return oci_rollback(db_conn());
}
