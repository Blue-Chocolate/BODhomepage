<?php

namespace App\Repositories;

use App\Models\Partner;
use Illuminate\Pagination\LengthAwarePaginator;

class PartnerRepository
{
    public function getAll(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return Partner::latest()
            ->paginate(
                perPage: min($perPage, 100),
                page: $page
            );
    }

    public function findById(int $id): Partner
    {
        return Partner::findOrFail($id);
    }
}