<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (['REVIEW'] as $table) {
    echo "=== {$table} ===\n";
    $cols = Illuminate\Support\Facades\DB::connection('oracle')->select(
        "SELECT column_name, data_type, nullable FROM user_tab_columns WHERE table_name = ? ORDER BY column_id",
        [$table]
    );
    foreach ($cols as $c) {
        echo "{$c->column_name} | {$c->data_type} | {$c->nullable}\n";
    }
    $cons = Illuminate\Support\Facades\DB::connection('oracle')->select(
        "SELECT cols.column_name, cons.constraint_name, cons.constraint_type
         FROM user_constraints cons
         JOIN user_cons_columns cols ON cons.constraint_name = cols.constraint_name
         WHERE cons.table_name = ?",
        [$table]
    );
    foreach ($cons as $c) {
        echo "  {$c->constraint_name} {$c->constraint_type} {$c->column_name}\n";
    }
    echo "\n";
}

$sample = Illuminate\Support\Facades\DB::table('REVIEW')->limit(2)->get();
print_r($sample->toArray());
