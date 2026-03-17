<?php

namespace App\Http\Controllers;

use App\Repositories\PartnerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function __construct(
        private readonly PartnerRepository $repository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $page    = (int) $request->query('page', 1);

        $partners = $this->repository->getAll($perPage, $page);

        return response()->json($partners);
    }

    public function show(int $id): JsonResponse
    {
        $partner = $this->repository->findById($id);

        return response()->json($partner);
    }
}