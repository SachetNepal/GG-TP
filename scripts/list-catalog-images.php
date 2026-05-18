<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (Illuminate\Support\Facades\DB::table('shop')->orderBy('shop_id')->get() as $s) {
    echo "SHOP {$s->shop_id} | {$s->shop_name} | ".substr((string) ($s->location ?? ''), 0, 60)."\n";
}

foreach (
    Illuminate\Support\Facades\DB::table('product as p')
        ->leftJoin('category as c', 'p.category_id', '=', 'c.category_id')
        ->select('p.product_id', 'p.product_name', 'p.shop_id', 'c.cat_name', 'p.description')
        ->orderBy('p.shop_id')
        ->orderBy('p.product_name')
        ->get() as $p
) {
    $desc = (string) ($p->description ?? '');
    $hasImg = str_contains($desc, '|IMG:') ? 'IMG' : '-';
    echo "PROD {$p->product_id} | {$p->shop_id} | {$p->cat_name} | {$p->product_name} | {$hasImg}\n";
}
