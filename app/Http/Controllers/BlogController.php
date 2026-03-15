<?php

namespace App\Http\Controllers;

use Orion\Http\Controllers\Controller as ApiController;

class BlogController extends ApiController
{
    protected $model = \App\Models\Blog::class;
       // Allow public access - no auth required
    protected function authorizationRequired(): bool
    {
        return false;
    }
}