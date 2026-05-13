<?php
/**
 * Dashboard aggregations for a shop. Quoted "ORDER" and "USER" for Oracle reserved words.
 * Adjust COLLECTION_SLOT column names if your physical columns differ (e.g. SLOT_DATE vs DATE).
 */
declare(strict_types=1);

/**
 * @return array<string, mixed>
 */
function trader_dashboard_data(int $shopId): array
{
    $out = [
        'revenue' => 0.0,
        'orders' => 0,
        'products' => 0,
        'slots' => 0,
        'last_updated' => (new DateTimeImmutable('now'))->format('Y-m-d H:i'),
        'week_label' => 'This week',
        'daily' => [],
        'upcoming' => [],
        'top_products' => [],
    ];

    if ($shopId < 1) {
        return $out;
    }

    $shopOrdersSub = 'SELECT DISTINCT oi.order_id FROM order_item oi
        INNER JOIN product p ON p.product_id = oi.product_id
        WHERE p.shop_id = :sid';

    try {
        $rev = (float) (db_fetch_scalar(
            "SELECT NVL(SUM(o.amount), 0) FROM \"ORDER\" o
             WHERE o.order_id IN ($shopOrdersSub)
             AND o.order_date >= TRUNC(SYSDATE, 'IW')
             AND o.order_date < TRUNC(SYSDATE, 'IW') + 7",
            ['sid' => $shopId]
        ) ?? 0);

        $ord = (int) (db_fetch_scalar(
            "SELECT COUNT(DISTINCT o.order_id) FROM \"ORDER\" o
             WHERE o.order_id IN ($shopOrdersSub)
             AND o.order_date >= TRUNC(SYSDATE, 'IW')
             AND o.order_date < TRUNC(SYSDATE, 'IW') + 7",
            ['sid' => $shopId]
        ) ?? 0);

        $act = (int) (db_fetch_scalar(
            'SELECT COUNT(*) FROM product
             WHERE shop_id = :sid AND product_in_stock > 0',
            ['sid' => $shopId]
        ) ?? 0);

        // Collection slots linked to this shop's orders (upcoming window optional)
        $slot = (int) (db_fetch_scalar(
            "SELECT COUNT(*) FROM collection_slot cs
             WHERE cs.order_id IN ($shopOrdersSub)",
            ['sid' => $shopId]
        ) ?? 0);

        $dailyRows = db_fetch_all(
            "SELECT TO_CHAR(TRUNC(o.order_date), 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') AS day_label,
                    NVL(SUM(o.amount), 0) AS amt
             FROM \"ORDER\" o
             WHERE o.order_id IN ($shopOrdersSub)
             AND o.order_date >= TRUNC(SYSDATE, 'IW')
             AND o.order_date < TRUNC(SYSDATE, 'IW') + 7
             GROUP BY TRUNC(o.order_date)
             ORDER BY TRUNC(o.order_date)",
            ['sid' => $shopId]
        );

        $daily = [];
        foreach ($dailyRows as $r) {
            $daily[] = [
                'day' => (string) ($r['day_label'] ?? '?'),
                'amount' => (float) ($r['amt'] ?? 0),
            ];
        }

        $upcoming = db_fetch_all(
            "SELECT * FROM (
                SELECT o.order_id, o.amount, o.status AS order_status,
                       TRIM(u.first_name || ' ' || u.last_name) AS customer_name,
                       o.order_date
                FROM \"ORDER\" o
                INNER JOIN \"USER\" u ON u.user_id = o.user_id
                WHERE o.order_id IN ($shopOrdersSub)
                ORDER BY o.order_date DESC
            ) WHERE ROWNUM <= 8",
            ['sid' => $shopId]
        );

        $top = db_fetch_all(
            "SELECT * FROM (
                SELECT p.product_name,
                       SUM(oi.quantity) AS order_count,
                       SUM(oi.quantity * oi.price) AS revenue,
                       p.product_in_stock AS stock,
                       CASE WHEN p.product_in_stock <= 0 THEN 'Out'
                            WHEN p.product_in_stock < 10 THEN 'Low'
                            ELSE 'OK' END AS stock_status
                FROM order_item oi
                INNER JOIN product p ON p.product_id = oi.product_id
                INNER JOIN \"ORDER\" o ON o.order_id = oi.order_id
                WHERE p.shop_id = :sid
                AND o.order_date >= TRUNC(SYSDATE, 'IW')
                GROUP BY p.product_id, p.product_name, p.product_in_stock
                ORDER BY SUM(oi.quantity * oi.price) DESC
            ) WHERE ROWNUM <= 10",
            ['sid' => $shopId]
        );

        $out['revenue'] = $rev;
        $out['orders'] = $ord;
        $out['products'] = $act;
        $out['slots'] = $slot;
        $out['daily'] = $daily;
        $out['upcoming'] = $upcoming;
        $out['top_products'] = $top;
    } catch (Throwable $e) {
        error_log('dashboard-queries: ' . $e->getMessage());
    }

    return $out;
}
