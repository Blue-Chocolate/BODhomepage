<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StrategicPlanResource;
use App\Models\StrategicPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StrategicPlanController extends Controller
{
    /**
     * GET /api/strategic-plans
     *
     * Supported query params:
     *   - per_page   (int, default 15, max 100)
     *   - status     (string)
     *   - categories (int)
     *   - search     (string — searches title + excerpt)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min((int) $request->input('per_page', 15), 100);

        $plans = StrategicPlan::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('categories'), fn ($q) => $q->where('categories', (int) $request->categories))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('excerpt', 'like', "%{$request->search}%");
            }))
            ->orderByDesc('post_date')
            ->paginate($perPage);

        return StrategicPlanResource::collection($plans);
    }

    /**
     * GET /api/strategic-plans/{strategicPlan}
     */
    public function show(StrategicPlan $strategicPlan): StrategicPlanResource
    {
        return new StrategicPlanResource($strategicPlan);
    }
}