<?php
/**
 * Trader report aggregations (daily / weekly / monthly).
 */
declare(strict_types=1);

function trader_shop_orders_subquery(): string
{
    return 'SELECT DISTINCT oi.order_id FROM order_item oi
        INNER JOIN product p ON p.product_id = oi.product_id
        WHERE p.shop_id = :sid';
}

/**
 * @return array{revenue: float, orders: int, rows: list<array<string, mixed>>}
 */
function trader_report_period(string $shopId, string $period): array
{
    $out = ['revenue' => 0.0, 'orders' => 0, 'rows' => []];
    if ($shopId === '') {
        return $out;
    }

    $sub = trader_shop_orders_subquery();
    $dateFilter = match ($period) {
        'daily' => "o.order_date >= TRUNC(SYSDATE) AND o.order_date < TRUNC(SYSDATE) + 1",
        'weekly' => "o.order_date >= TRUNC(SYSDATE, 'IW') AND o.order_date < TRUNC(SYSDATE, 'IW') + 7",
        'monthly' => "o.order_date >= TRUNC(SYSDATE, 'MM') AND o.order_date < ADD_MONTHS(TRUNC(SYSDATE, 'MM'), 1)",
        default => "o.order_date >= TRUNC(SYSDATE, 'IW') AND o.order_date < TRUNC(SYSDATE, 'IW') + 7",
    };

    try {
        $out['revenue'] = (float) (db_fetch_scalar(
            "SELECT NVL(SUM(o.amount), 0) FROM orders o
             WHERE o.order_id IN ($sub) AND $dateFilter",
            ['sid' => $shopId]
        ) ?? 0);

        $out['orders'] = (int) (db_fetch_scalar(
            "SELECT COUNT(DISTINCT o.order_id) FROM orders o
             WHERE o.order_id IN ($sub) AND $dateFilter",
            ['sid' => $shopId]
        ) ?? 0);

        $groupBy = match ($period) {
            'daily' => "TO_CHAR(o.order_date, 'HH24')",
            'monthly' => "TO_CHAR(TRUNC(o.order_date), 'DD')",
            default => "TO_CHAR(TRUNC(o.order_date), 'DY', 'NLS_DATE_LANGUAGE=ENGLISH')",
        };

        $out['rows'] = db_fetch_all(
            "SELECT $groupBy AS label, NVL(SUM(o.amount), 0) AS amt,
                    COUNT(DISTINCT o.order_id) AS ord_count
             FROM orders o
             WHERE o.order_id IN ($sub) AND $dateFilter
             GROUP BY $groupBy
             ORDER BY MIN(o.order_date)",
            ['sid' => $shopId]
        );
    } catch (Throwable $e) {
        error_log('report-queries: ' . $e->getMessage());
    }

    return $out;
}

/**
 * @return list<array<string, mixed>>
 */
function trader_report_top_products(string $shopId, string $period, int $limit = 10): array
{
    if ($shopId === '') {
        return [];
    }

    $sub = trader_shop_orders_subquery();
    $dateFilter = match ($period) {
        'daily' => "o.order_date >= TRUNC(SYSDATE) AND o.order_date < TRUNC(SYSDATE) + 1",
        'weekly' => "o.order_date >= TRUNC(SYSDATE, 'IW') AND o.order_date < TRUNC(SYSDATE, 'IW') + 7",
        'monthly' => "o.order_date >= TRUNC(SYSDATE, 'MM') AND o.order_date < ADD_MONTHS(TRUNC(SYSDATE, 'MM'), 1)",
        default => "o.order_date >= TRUNC(SYSDATE, 'IW') AND o.order_date < TRUNC(SYSDATE, 'IW') + 7",
    };

    try {
        return db_fetch_all(
            "SELECT * FROM (
                SELECT p.product_name,
                       SUM(oi.quantity) AS order_count,
                       SUM(oi.quantity * oi.price) AS revenue
                FROM order_item oi
                INNER JOIN product p ON p.product_id = oi.product_id
                INNER JOIN orders o ON o.order_id = oi.order_id
                WHERE p.shop_id = :sid AND $dateFilter
                GROUP BY p.product_id, p.product_name
                ORDER BY SUM(oi.quantity * oi.price) DESC
            ) WHERE ROWNUM <= :lim",
            ['sid' => $shopId, 'lim' => $limit]
        );
    } catch (Throwable $e) {
        return [];
    }
}
