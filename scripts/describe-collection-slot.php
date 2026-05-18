<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (['COLLECTION_SLOT', 'ORDERS'] as $table) {
    echo "=== {$table} ===\n";
    $cols = Illuminate\Support\Facades\DB::connection('oracle')->select(
        "SELECT column_name, data_type, nullable FROM user_tab_columns WHERE table_name = ? ORDER BY column_id",
        [$table]
    );
    foreach ($cols as $c) {
        echo "{$c->column_name} | {$c->data_type} | {$c->nullable}\n";
    }
    echo "\n";
}

exit(0);

$cols = Illuminate\Support\Facades\DB::connection('oracle')->select(
    "SELECT column_name, data_type, nullable FROM user_tab_columns WHERE table_name = 'COLLECTION_SLOT' ORDER BY column_id"
);
foreach ($cols as $c) {
    echo "{$c->column_name} | {$c->data_type} | {$c->nullable}\n";
}

$sample = Illuminate\Support\Facades\DB::table('COLLECTION_SLOT')->orderByDesc('slot_id')->limit(3)->get();
print_r($sample->toArray());
