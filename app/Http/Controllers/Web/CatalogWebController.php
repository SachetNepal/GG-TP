<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Catalog\CatalogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogWebController extends Controller
{
    public function __construct(protected CatalogService $catalogService)
    {
    }

    public function categories(Request $request): View
    {
        $filters = [
            'q' => $request->query('q'),
            'category_id' => array_values(array_filter((array) $request->query('category_id', []))),
            'shop_id' => array_values(array_filter((array) $request->query('shop_id', []))),
        ];

        return view('categories.index', [
            'products' => $this->catalogService->products($filters),
            'categories' => $this->catalogService->categories(),
            'shops' => \App\Models\Shop::query()->orderBy('shop_name')->get(),
            'filters' => $filters,
        ]);
    }

    public function show(string $id): View
    {
        $product = $this->catalogService->productDetail($id);

        return view('products.show', [
            'product' => $product,
            'productId' => $id,
        ]);
    }

    public function shops(): View
    {
        $products = $this->catalogService->products([]);

        $traders = $products->getCollection()
            ->pluck('shop')
            ->filter()
            ->unique('shop_id')
            ->values();

        return view('traders.index', ['traders' => $traders]);
    }
}
