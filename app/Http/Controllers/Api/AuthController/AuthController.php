<?php

namespace App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Controller;
use App\Models\Organization\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    // ─── Register ────────────────────────────────────────────────────────────

    public function register(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                // User fields
                'name'                  => ['required', 'string', 'max:255'],
                'email'                 => ['required', 'email', 'unique:users,email'],
                'password'              => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],

                // Organization fields
                'org_name'              => ['required', 'string', 'max:255'],
                'org_email'             => ['required', 'email', 'unique:organizations,email'],
                'org_phone'             => ['required', 'string', 'max:20'],
                'org_license_number'    => ['required', 'string', 'unique:organizations,liscense_number'],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();

        try {
            $organization = Organization::create([
                'name'            => $validated['org_name'],
                'email'           => $validated['org_email'],
                'phone'           => $validated['org_phone'],
                'liscense_number' => $validated['org_license_number'],
                'approval_status' => 'pending',
            ]);

            $user = User::create([
                'name'            => $validated['name'],
                'email'           => $validated['email'],
                'password'        => Hash::make($validated['password']),
                'organization_id' => $organization->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration successful. Awaiting organization approval.',
                'data'    => [
                    'user'         => $user->only('id', 'name', 'email'),
                    'organization' => $organization->only('id', 'name', 'approval_status'),
                ],
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // ─── Login ────────────────────────────────────────────────────────────────

    public function login(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email'    => ['required', 'email'],
                'password' => ['required', 'string'],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $user = User::with('organization')->where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email or password.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Soft org check at login — gives a specific message before token issuance
            if (!$user->organization) {
                return response()->json([
                    'success' => false,
                    'message' => 'No organization is linked to this account.',
                ], Response::HTTP_FORBIDDEN);
            }

            if ($user->organization->approval_status !== 'approved') {
                return response()->json([
                    'success'         => false,
                    'message'         => 'Your organization is pending approval. You cannot log in yet.',
                    'approval_status' => $user->organization->approval_status,
                ], Response::HTTP_FORBIDDEN);
            }

            // Revoke old tokens and issue fresh one
            $user->tokens()->delete();
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'data'    => [
                    'token'        => $token,
                    'user'         => $user->only('id', 'name', 'email'),
                    'organization' => $user->organization->only('id', 'name', 'approval_status'),
                ],
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Login failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Login failed. Please try again.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Logout failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Logout failed. Please try again.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}