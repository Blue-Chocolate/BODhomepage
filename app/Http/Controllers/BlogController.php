<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogRequest;
use Orion\Http\Controllers\Controller as ApiController;

class BlogController extends ApiController
{
    protected $model = \App\Models\Blog::class;
    protected $request = StoreBlogRequest::class;

    protected function authorizationRequired(): bool
    {
        return false;
    }
}