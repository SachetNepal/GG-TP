<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Trader\TraderDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TraderDashboardController extends Controller
{
    public function __construct(protected TraderDashboardService $service)
    {
    }

    public function summary(Request $request): JsonResponse
    {
        return response()->json($this->service->summary($request->user()));
    }
}

