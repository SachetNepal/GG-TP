<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (['USERS', 'VERIFICATION', 'CUSTOMER'] as $table) {
    echo "=== {$table} ===\n";
    $cols = Illuminate\Support\Facades\DB::connection('oracle')->select(
        "SELECT column_name, data_type, data_length, nullable FROM user_tab_columns WHERE table_name = ? ORDER BY column_id",
        [$table]
    );
    foreach ($cols as $c) {
        echo "{$c->column_name} | {$c->data_type} | {$c->data_length} | {$c->nullable}\n";
    }
    echo "\n";
}
