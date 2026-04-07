<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckApprovedOrganization
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please log in.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $organization = $user->organization;

            if (!$organization) {
                return response()->json([
                    'success' => false,
                    'message' => 'No organization is associated with this account.',
                ], Response::HTTP_FORBIDDEN);
            }

            if ($organization->approval_status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Your organization is not approved yet. Please contact support.',
                    'approval_status' => $organization->approval_status,
                ], Response::HTTP_FORBIDDEN);
            }

            // Attach organization to request for downstream use
            $request->merge(['organization' => $organization]);

            return $next($request);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while verifying organization status.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}