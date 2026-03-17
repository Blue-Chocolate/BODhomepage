<?php

namespace App\Repositories;

use App\Models\ContactUs;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactUsRepository
{
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return ContactUs::latest()->paginate($perPage);
    }

    public function findById(int $id): ContactUs
    {
        return ContactUs::findOrFail($id);
    }

    public function create(array $data): ContactUs
    {
        return ContactUs::create($data);
    }

    public function update(ContactUs $contactUs, array $data): ContactUs
    {
        $contactUs->update($data);
        return $contactUs->fresh();
    }

    public function delete(ContactUs $contactUs): void
    {
        $contactUs->delete();
    }
}