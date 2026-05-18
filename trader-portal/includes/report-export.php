<?php

declare(strict_types=1);

require_once __DIR__ . '/report-queries.php';

function trader_report_xml_escape(string $value): string
{
    return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

/**
 * @param list<array<string, mixed>> $rows
 */
function trader_report_xml_row(array $cells, bool $header = false): string
{
    $xml = '<Row>';
    foreach ($cells as $cell) {
        $type = is_int($cell) || is_float($cell) ? 'Number' : 'String';
        $value = trader_report_xml_escape((string) $cell);
        if ($header) {
            $xml .= '<Cell ss:StyleID="Header"><Data ss:Type="' . $type . '">' . $value . '</Data></Cell>';
        } else {
            $xml .= '<Cell><Data ss:Type="' . $type . '">' . $value . '</Data></Cell>';
        }
    }
    $xml .= '</Row>';

    return $xml;
}

/**
 * @param array{revenue: float, orders: int, rows: list<array<string, mixed>>} $report
 * @param list<array<string, mixed>> $top
 * @param array<string, mixed> $context
 */
function trader_report_worksheet_xml(
    string $sheetName,
    string $shopName,
    array $context,
    array $report,
    array $top
): string {
    $periodLabel = (string) ($context['period_label'] ?? 'Report');
    $rangeLabel = (string) ($context['range_label'] ?? '');

    $rows = '';
    $rows .= trader_report_xml_row(['GroceryGO Trader Report — ' . $periodLabel]);
    $rows .= trader_report_xml_row(['Shop', $shopName]);
    $rows .= trader_report_xml_row(['Period', $rangeLabel]);
    $rows .= trader_report_xml_row(['Generated', date('Y-m-d H:i:s')]);
    $rows .= trader_report_xml_row([]);

    $rows .= trader_report_xml_row(['Summary'], true);
    $rows .= trader_report_xml_row(['Total revenue (USD)', number_format((float) ($report['revenue'] ?? 0), 2, '.', '')]);
    $rows .= trader_report_xml_row(['Total orders', (int) ($report['orders'] ?? 0)]);
    $rows .= trader_report_xml_row([]);

    $rows .= trader_report_xml_row(['Breakdown'], true);
    $rows .= trader_report_xml_row(['Period label', 'Orders', 'Revenue (USD)'], true);
    if ($report['rows'] === []) {
        $rows .= trader_report_xml_row(['No breakdown data for this period', '', '']);
    } else {
        foreach ($report['rows'] as $row) {
            $rows .= trader_report_xml_row([
                (string) ($row['label'] ?? ''),
                (int) ($row['ord_count'] ?? 0),
                number_format((float) ($row['amt'] ?? 0), 2, '.', ''),
            ]);
        }
    }
    $rows .= trader_report_xml_row([]);

    $rows .= trader_report_xml_row(['Top products'], true);
    $rows .= trader_report_xml_row(['Product', 'Units sold', 'Revenue (USD)'], true);
    if ($top === []) {
        $rows .= trader_report_xml_row(['No product sales in this period', '', '']);
    } else {
        foreach ($top as $row) {
            $rows .= trader_report_xml_row([
                (string) ($row['product_name'] ?? ''),
                (int) ($row['order_count'] ?? 0),
                number_format((float) ($row['revenue'] ?? 0), 2, '.', ''),
            ]);
        }
    }

    $name = trader_report_xml_escape($sheetName);

    return '<Worksheet ss:Name="' . $name . '"><Table>' . $rows . '</Table></Worksheet>';
}

/**
 * @param list<array{context: array<string, mixed>, sheet: string}> $sheets
 */
function trader_report_send_workbook_sheets(string $shopId, string $shopName, array $sheets, string $filename): void
{
    $displayShop = $shopName !== '' ? $shopName : $shopId;

    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');

    $worksheets = '';
    foreach ($sheets as $sheet) {
        $ctx = $sheet['context'];
        $report = trader_report_period_data($shopId, $ctx);
        $top = trader_report_top_products_data($shopId, $ctx, 100);
        $worksheets .= trader_report_worksheet_xml(
            (string) $sheet['sheet'],
            $displayShop,
            $ctx,
            $report,
            $top
        );
    }

    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
    echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"';
    echo ' xmlns:o="urn:schemas-microsoft-com:office:office"';
    echo ' xmlns:x="urn:schemas-microsoft-com:office:excel"';
    echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"';
    echo ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
    echo '<Styles>';
    echo '<Style ss:ID="Header"><Font ss:Bold="1"/><Interior ss:Color="#E8F5E9" ss:Pattern="Solid"/></Style>';
    echo '</Styles>';
    echo $worksheets;
    echo '</Workbook>';
    exit;
}

/**
 * Export one or all report periods for the given range selectors.
 *
 * @param 'daily'|'weekly'|'monthly'|'all' $scope
 */
function trader_report_send_export(
    string $shopId,
    string $shopName,
    string $scope,
    ?string $date = null,
    ?string $week = null,
    ?string $month = null
): void {
    $stamp = date('Y-m-d');

    if ($scope === 'all') {
        $sheets = [];
        foreach (TRADER_REPORT_PERIOD_LABELS as $period => $label) {
            $sheets[] = [
                'sheet' => $label,
                'context' => trader_report_build_context($period, $date, $week, $month),
            ];
        }
        trader_report_send_workbook_sheets($shopId, $shopName, $sheets, 'grocerygo-reports-' . $stamp . '.xls');

        return;
    }

    if (! isset(TRADER_REPORT_PERIOD_LABELS[$scope])) {
        $scope = 'weekly';
    }

    $ctx = trader_report_build_context($scope, $date, $week, $month);
    $short = (string) $ctx['range_short'];
    $filename = 'grocerygo-' . $scope . '-' . str_replace(['/', ' '], '-', $short) . '.xls';

    trader_report_send_workbook_sheets($shopId, $shopName, [
        ['sheet' => TRADER_REPORT_PERIOD_LABELS[$scope], 'context' => $ctx],
    ], $filename);
}
