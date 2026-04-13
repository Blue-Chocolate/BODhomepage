<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SocialInitiativeResource;
use App\Models\SocialInitiative;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class SocialInitiativeController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $initiatives = SocialInitiative::published()
            ->when($request->category_id, fn ($q, $v) => $q->where('category_id', $v))
            ->latest('post_date')
            ->paginate($request->integer('per_page', 15));

        return SocialInitiativeResource::collection($initiatives);
    }

    public function show(SocialInitiative $socialInitiative): SocialInitiativeResource
    {
        return new SocialInitiativeResource($socialInitiative);
    }
}