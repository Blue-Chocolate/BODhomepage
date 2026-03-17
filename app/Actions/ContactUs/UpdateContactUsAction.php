<?php

namespace App\Actions\ContactUs;

use App\Models\ContactUs;
use App\Repositories\ContactUsRepository;

class UpdateContactUsAction
{
    public function __construct(
        private readonly ContactUsRepository $repository
    ) {}

    public function execute(ContactUs $contactUs, array $data): ContactUs
    {
        return $this->repository->update($contactUs, $data);
    }
}