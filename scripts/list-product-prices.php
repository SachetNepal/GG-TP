<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$rows = Illuminate\Support\Facades\DB::table('product')
    ->select('product_id', 'product_name', 'price')
    ->orderBy('product_id')
    ->get();

foreach ($rows as $r) {
    echo "{$r->product_id} | {$r->product_name} | {$r->price}\n";
}
