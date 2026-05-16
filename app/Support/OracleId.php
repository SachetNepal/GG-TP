<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

final class OracleId
{
    /**
     * Generate next prefixed id (e.g. BI + 2 => BI3) from existing rows.
     */
    public static function next(string $table, string $column, string $prefix): string
    {
        $rows = DB::connection('oracle')->table($table)->select($column)->get();
        $max = 0;
        foreach ($rows as $row) {
            $val = (string) ($row->{$column} ?? '');
            if (preg_match('/(\d+)$/', $val, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }

        return $prefix . ($max + 1);
    }
}
