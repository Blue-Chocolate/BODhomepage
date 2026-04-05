<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DigitalSolutionLink;
use Illuminate\Http\JsonResponse;

class DigitalSolutionLinkController extends Controller
{
    public function index(): JsonResponse
    {
        $links = DigitalSolutionLink::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $links,
        ]);
    }

    public function show(DigitalSolutionLink $digitalSolutionLink): JsonResponse
    {
        if (!$digitalSolutionLink->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $digitalSolutionLink,
        ]);
    }
}