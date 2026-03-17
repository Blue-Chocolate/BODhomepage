<?php

namespace App\Actions\ContactUs;

use App\Models\ContactUs;
use App\Repositories\ContactUsRepository;

class DeleteContactUsAction
{
    public function __construct(
        private readonly ContactUsRepository $repository
    ) {}

    public function execute(ContactUs $contactUs): void
    {
        $this->repository->delete($contactUs);
    }
}