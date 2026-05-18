<?php
/**
 * Trader report aggregations (daily / weekly / monthly) with selectable ranges.
 */
declare(strict_types=1);

/** @var array<string, string> */
const TRADER_REPORT_PERIOD_LABELS = [
    'daily' => 'Daily',
    'weekly' => 'Weekly',
    'monthly' => 'Monthly',
];

function trader_shop_orders_subquery(): string
{
    return 'SELECT DISTINCT oi.order_id FROM order_item oi
        INNER JOIN product p ON p.product_id = oi.product_id
        WHERE p.shop_id = :sid';
}

function trader_report_date_filter_sql(): string
{
    return "o.order_date >= TO_DATE(:d_start, 'YYYY-MM-DD')
            AND o.order_date < TO_DATE(:d_end, 'YYYY-MM-DD')";
}

function trader_report_sanitize_date(?string $value): ?string
{
    if ($value === null || $value === '') {
        return null;
    }
    $dt = DateTimeImmutable::createFromFormat('Y-m-d', $value);

    return $dt && $dt->format('Y-m-d') === $value ? $value : null;
}

function trader_report_sanitize_week(?string $value): ?string
{
    if ($value === null || $value === '') {
        return null;
    }
    if (! preg_match('/^(\d{4})-W(\d{2})$/', $value, $m)) {
        return null;
    }
    $year = (int) $m[1];
    $week = (int) $m[2];
    if ($week < 1 || $week > 53) {
        return null;
    }
    $dt = new DateTimeImmutable('now');
    $dt = $dt->setISODate($year, $week);
    if ((int) $dt->format('o') !== $year) {
        return null;
    }

    return sprintf('%04d-W%02d', $year, $week);
}

function trader_report_current_week_key(DateTimeImmutable $dt): string
{
    return sprintf('%04d-W%02d', (int) $dt->format('o'), (int) $dt->format('W'));
}

function trader_report_sanitize_month(?string $value): ?string
{
    if ($value === null || $value === '') {
        return null;
    }
    $dt = DateTimeImmutable::createFromFormat('Y-m', $value);

    return $dt && $dt->format('Y-m') === $value ? $value : null;
}

/**
 * @return array{start: string, end: string, label: string, short: string}
 */
function trader_report_daily_bounds(string $date): array
{
    $start = new DateTimeImmutable($date);
    $end = $start->modify('+1 day');

    return [
        'start' => $start->format('Y-m-d'),
        'end' => $end->format('Y-m-d'),
        'label' => $start->format('l, j F Y'),
        'short' => $start->format('Y-m-d'),
    ];
}

/**
 * @return array{start: string, end: string, label: string, short: string}|null
 */
function trader_report_week_bounds(string $week): ?array
{
    if (! preg_match('/^(\d{4})-W(\d{2})$/', $week, $m)) {
        return null;
    }
    $start = (new DateTimeImmutable('now'))->setISODate((int) $m[1], (int) $m[2]);
    $end = $start->modify('+7 days');
    $rangeEnd = $end->modify('-1 day');

    return [
        'start' => $start->format('Y-m-d'),
        'end' => $end->format('Y-m-d'),
        'label' => sprintf(
            'Week %s, %s (%s – %s)',
            $m[2],
            $m[1],
            $start->format('j M'),
            $rangeEnd->format('j M Y')
        ),
        'short' => $week,
    ];
}

/**
 * @return array{start: string, end: string, label: string, short: string}
 */
function trader_report_month_bounds(string $month): array
{
    $start = DateTimeImmutable::createFromFormat('Y-m-d', $month . '-01');
    if (! $start) {
        $start = new DateTimeImmutable('first day of this month');
    }
    $end = $start->modify('+1 month');

    return [
        'start' => $start->format('Y-m-d'),
        'end' => $end->format('Y-m-d'),
        'label' => $start->format('F Y'),
        'short' => $start->format('Y-m'),
    ];
}

/**
 * Build report query context for a period and range selectors.
 *
 * @return array{
 *   period: string,
 *   period_label: string,
 *   date_start: string,
 *   date_end: string,
 *   range_label: string,
 *   range_short: string,
 *   group_by: string,
 *   date: string,
 *   week: string,
 *   month: string
 * }
 */
function trader_report_build_context(
    string $period,
    ?string $date = null,
    ?string $week = null,
    ?string $month = null
): array {
    if (! isset(TRADER_REPORT_PERIOD_LABELS[$period])) {
        $period = 'weekly';
    }

    $today = new DateTimeImmutable('today');

    $date = trader_report_sanitize_date($date) ?? $today->format('Y-m-d');
    $week = trader_report_sanitize_week($week) ?? trader_report_current_week_key($today);
    $month = trader_report_sanitize_month($month) ?? $today->format('Y-m');

    // Do not allow future daily dates
    if ($date > $today->format('Y-m-d')) {
        $date = $today->format('Y-m-d');
    }

    $bounds = match ($period) {
        'daily' => trader_report_daily_bounds($date),
        'monthly' => trader_report_month_bounds($month),
        default => trader_report_week_bounds($week) ?? trader_report_week_bounds(trader_report_current_week_key($today)),
    };

    if ($bounds === null) {
        $bounds = trader_report_daily_bounds($today->format('Y-m-d'));
    }

    $groupBy = match ($period) {
        'daily' => "TO_CHAR(o.order_date, 'HH24')",
        'monthly' => "TO_CHAR(TRUNC(o.order_date), 'DD')",
        default => "TO_CHAR(TRUNC(o.order_date), 'DY', 'NLS_DATE_LANGUAGE=ENGLISH')",
    };

    return [
        'period' => $period,
        'period_label' => TRADER_REPORT_PERIOD_LABELS[$period],
        'date_start' => $bounds['start'],
        'date_end' => $bounds['end'],
        'range_label' => $bounds['label'],
        'range_short' => $bounds['short'],
        'group_by' => $groupBy,
        'date' => $date,
        'week' => $week,
        'month' => $month,
    ];
}

/**
 * @param array<string, mixed> $context
 * @return array<string, mixed>
 */
function trader_report_bind_context(array $context, string $shopId): array
{
    return [
        'sid' => $shopId,
        'd_start' => (string) $context['date_start'],
        'd_end' => (string) $context['date_end'],
    ];
}

/**
 * @param array<string, mixed> $context
 * @return array{revenue: float, orders: int, rows: list<array<string, mixed>>}
 */
function trader_report_period_data(string $shopId, array $context): array
{
    $out = ['revenue' => 0.0, 'orders' => 0, 'rows' => []];
    if ($shopId === '') {
        return $out;
    }

    $sub = trader_shop_orders_subquery();
    $filter = trader_report_date_filter_sql();
    $binds = trader_report_bind_context($context, $shopId);
    $groupBy = (string) $context['group_by'];

    try {
        $out['revenue'] = (float) (db_fetch_scalar(
            "SELECT NVL(SUM(o.amount), 0) FROM orders o
             WHERE o.order_id IN ($sub) AND $filter",
            $binds
        ) ?? 0);

        $out['orders'] = (int) (db_fetch_scalar(
            "SELECT COUNT(DISTINCT o.order_id) FROM orders o
             WHERE o.order_id IN ($sub) AND $filter",
            $binds
        ) ?? 0);

        $out['rows'] = db_fetch_all(
            "SELECT $groupBy AS label, NVL(SUM(o.amount), 0) AS amt,
                    COUNT(DISTINCT o.order_id) AS ord_count
             FROM orders o
             WHERE o.order_id IN ($sub) AND $filter
             GROUP BY $groupBy
             ORDER BY MIN(o.order_date)",
            $binds
        );
    } catch (Throwable $e) {
        error_log('report-queries: ' . $e->getMessage());
    }

    return $out;
}

/** @deprecated Use trader_report_period_data */
function trader_report_period(string $shopId, string $period): array
{
    $ctx = trader_report_build_context($period);

    return trader_report_period_data($shopId, $ctx);
}

/**
 * @param array<string, mixed> $context
 * @return list<array<string, mixed>>
 */
function trader_report_top_products_data(string $shopId, array $context, int $limit = 10): array
{
    if ($shopId === '') {
        return [];
    }

    $sub = trader_shop_orders_subquery();
    $filter = trader_report_date_filter_sql();
    $binds = trader_report_bind_context($context, $shopId);
    $binds['lim'] = $limit;

    try {
        return db_fetch_all(
            "SELECT * FROM (
                SELECT p.product_name,
                       SUM(oi.quantity) AS order_count,
                       SUM(oi.quantity * oi.price) AS revenue
                FROM order_item oi
                INNER JOIN product p ON p.product_id = oi.product_id
                INNER JOIN orders o ON o.order_id = oi.order_id
                WHERE p.shop_id = :sid AND $filter
                GROUP BY p.product_id, p.product_name
                ORDER BY SUM(oi.quantity * oi.price) DESC
            ) WHERE ROWNUM <= :lim",
            $binds
        );
    } catch (Throwable $e) {
        return [];
    }
}

/** @deprecated Use trader_report_top_products_data */
function trader_report_top_products(string $shopId, string $period, int $limit = 10): array
{
    $ctx = trader_report_build_context($period);

    return trader_report_top_products_data($shopId, $ctx, $limit);
}

/**
 * @return list<string> ISO week keys (YYYY-Www), newest first
 */
function trader_report_available_weeks(string $shopId, int $limit = 52): array
{
    if ($shopId === '') {
        return [];
    }

    $sub = trader_shop_orders_subquery();
    $today = new DateTimeImmutable('today');
    $binds = [
        'sid' => $shopId,
        'd_start' => $today->modify('-2 years')->format('Y-m-d'),
        'd_end' => $today->modify('+1 day')->format('Y-m-d'),
        'lim' => $limit,
    ];

    try {
        $rows = db_fetch_all(
            "SELECT * FROM (
                SELECT DISTINCT TO_CHAR(TRUNC(o.order_date, 'IW'), 'IYYY')
                       || '-W' || LPAD(TO_CHAR(TRUNC(o.order_date, 'IW'), 'IW'), 2, '0') AS week_key
                FROM orders o
                WHERE o.order_id IN ($sub)
                  AND o.order_date >= TO_DATE(:d_start, 'YYYY-MM-DD')
                  AND o.order_date < TO_DATE(:d_end, 'YYYY-MM-DD')
                ORDER BY week_key DESC
            ) WHERE ROWNUM <= :lim",
            $binds
        );
    } catch (Throwable $e) {
        return [];
    }

    $weeks = [];
    foreach ($rows as $row) {
        $key = trader_report_sanitize_week((string) ($row['week_key'] ?? ''));
        if ($key !== null) {
            $weeks[] = $key;
        }
    }

    $current = trader_report_current_week_key($today);
    if (! in_array($current, $weeks, true)) {
        array_unshift($weeks, $current);
    }

    return array_values(array_unique($weeks));
}

/**
 * @return list<string> YYYY-MM keys, newest first
 */
function trader_report_available_months(string $shopId, int $limit = 24): array
{
    if ($shopId === '') {
        return [];
    }

    $sub = trader_shop_orders_subquery();
    $today = new DateTimeImmutable('today');
    $binds = [
        'sid' => $shopId,
        'd_start' => $today->modify('-3 years')->format('Y-m-d'),
        'd_end' => $today->modify('+1 day')->format('Y-m-d'),
        'lim' => $limit,
    ];

    try {
        $rows = db_fetch_all(
            "SELECT * FROM (
                SELECT DISTINCT TO_CHAR(TRUNC(o.order_date, 'MM'), 'YYYY-MM') AS month_key
                FROM orders o
                WHERE o.order_id IN ($sub)
                  AND o.order_date >= TO_DATE(:d_start, 'YYYY-MM-DD')
                  AND o.order_date < TO_DATE(:d_end, 'YYYY-MM-DD')
                ORDER BY month_key DESC
            ) WHERE ROWNUM <= :lim",
            $binds
        );
    } catch (Throwable $e) {
        return [];
    }

    $months = [];
    foreach ($rows as $row) {
        $key = trader_report_sanitize_month((string) ($row['month_key'] ?? ''));
        if ($key !== null) {
            $months[] = $key;
        }
    }

    $current = $today->format('Y-m');
    if (! in_array($current, $months, true)) {
        array_unshift($months, $current);
    }

    return array_values(array_unique($months));
}

/**
 * @param array<string, string|null> $params
 */
function trader_report_export_query(array $params): string
{
    $allowed = ['period', 'date', 'week', 'month', 'scope'];
    $parts = [];
    foreach ($allowed as $key) {
        if (! isset($params[$key]) || $params[$key] === null || $params[$key] === '') {
            continue;
        }
        $parts[] = rawurlencode($key) . '=' . rawurlencode((string) $params[$key]);
    }

    return $parts === [] ? '' : '?' . implode('&', $parts);
}
