<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $query = Organization::query();

        if ($request->filled('status')) {
            $query->where('approval_status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('liscense_number', 'like', "%{$request->search}%");
            });
        }

        return response()->json(
            $query->latest()->paginate($request->get('per_page', 15))
        );
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'name'                  => 'required|string|max:255',
        'liscense_number'       => 'required|string|unique:organizations,liscense_number',
        'evaluation_duration'   => 'nullable|integer|min:0',
        'type'                  => ['required', Rule::in(['government', 'foundation', 'non_profit'])],
        'evaluator_name'        => 'nullable|string|max:255',
        'evaluation_team'       => 'nullable|string|max:255',
        'representative_name'   => 'nullable|string|max:255',
    ]);

    $organization = Organization::create($data);
    
    // Generate a token for the organization
    $token = $organization->createToken('auth_token')->plainTextToken;

    return response()->json([
        'organization' => $organization,
        'token' => $token,
        'token_type' => 'Bearer',
    ], 201);
}

    public function show(Organization $organization)
    {
        return response()->json($organization);
    }

    public function update(Request $request, Organization $organization)
    {
        $data = $request->validate([
            'name'                  => 'sometimes|required|string|max:255',
            'email'                 => ['nullable', 'email', Rule::unique('organizations', 'email')->ignore($organization->id)],
            'phone'                 => 'nullable|string|max:20',
            'liscense_number'       => ['sometimes', 'required', 'string', Rule::unique('organizations', 'liscense_number')->ignore($organization->id)],
            'evaluation_date'       => 'nullable|date',
            'evaluation_duration'   => 'nullable|integer|min:0',
            'evaluation_score'      => 'nullable|numeric|min:0|max:100',
            'evaluator_name'        => 'nullable|string|max:255',
            'evaluation_team'       => 'nullable|string|max:255',
            'representative_name'   => 'nullable|string|max:255',
        ]);

        $organization->update($data);

        return response()->json($organization);
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return response()->json(['message' => 'Organization deleted.']);
    }

    public function approve(Organization $organization)
    {
        if (!$organization->isPending()) {
            return response()->json(['message' => 'Organization is not pending.'], 422);
        }

        $organization->approve();

        return response()->json(['message' => 'Organization approved.', 'data' => $organization]);
    }

    public function reject(Organization $organization)
    {
        if (!$organization->isPending()) {
            return response()->json(['message' => 'Organization is not pending.'], 422);
        }

        $organization->reject();

        return response()->json(['message' => 'Organization rejected.', 'data' => $organization]);
    }
}