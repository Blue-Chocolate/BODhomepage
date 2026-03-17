<?php

namespace App\Actions\ContactUs;

use App\Models\ContactUs;
use App\Repositories\ContactUsRepository;

class StoreContactUsAction
{
    public function __construct(
        private readonly ContactUsRepository $repository
    ) {}

    public function execute(array $data): ContactUs
    {
        return $this->repository->create($data);
    }
}