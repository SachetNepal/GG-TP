<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$triggers = Illuminate\Support\Facades\DB::select(
    "SELECT trigger_name, trigger_type, triggering_event FROM user_triggers WHERE table_name = 'BASKET'"
);
print_r($triggers);

try {
    Illuminate\Support\Facades\DB::insert(
        'INSERT INTO BASKET (BASKET_ID, CUSTOMER_ID, CREATED_DATE) VALUES (?, ?, SYSDATE)',
        ['BA99', 'U13']
    );
    echo "raw insert BA99 ok\n";
} catch (Throwable $e) {
    echo 'raw insert failed: '.$e->getMessage()."\n";
}
