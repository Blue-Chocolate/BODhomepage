<?php
// app/Http/Controllers/Api/StrategicPlanController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StrategicPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StrategicPlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $plans = StrategicPlan::published()
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->orderByDesc('published_at')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'data'  => $plans->items(),
            'meta'  => [
                'current_page' => $plans->currentPage(),
                'last_page'    => $plans->lastPage(),
                'total'        => $plans->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $plan = StrategicPlan::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json(['data' => $plan]);
    }
}