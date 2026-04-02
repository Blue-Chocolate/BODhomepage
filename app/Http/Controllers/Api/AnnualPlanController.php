<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnualPlanRequest;
use App\Http\Resources\AnnualPlanResource;
use App\Models\AnnualPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AnnualPlanController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $plans = AnnualPlan::query()
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->category_id, fn ($q, $v) => $q->where('category_id', $v))
            ->when($request->search, fn ($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('title', 'like', "%{$v}%")
                  ->orWhere('excerpt', 'like', "%{$v}%");
            }))
            ->latest('published_at')
            ->paginate($request->integer('per_page', 15));

        return AnnualPlanResource::collection($plans);
    }

    public function store(AnnualPlanRequest $request): JsonResponse
    {
        $plan = AnnualPlan::create($request->validated());

        return (new AnnualPlanResource($plan))
            ->response()
            ->setStatusCode(201);
    }

    public function show(AnnualPlan $annualPlan): AnnualPlanResource
    {
        return new AnnualPlanResource($annualPlan);
    }

    public function update(AnnualPlanRequest $request, AnnualPlan $annualPlan): AnnualPlanResource
    {
        $annualPlan->update($request->validated());

        return new AnnualPlanResource($annualPlan->refresh());
    }

    public function destroy(AnnualPlan $annualPlan): JsonResponse
    {
        $annualPlan->delete();

        return response()->json(['message' => 'Deleted successfully.']);
    }
}