<?php

namespace App\Http\Controllers;

use Orion\Http\Controllers\Controller as ApiController;
use App\Http\Requests\StoreBlogRequest;

class BlogController extends ApiController
{
    protected $model = \App\Models\Blog::class;
    protected $request = StoreBlogRequest::class;

       // Allow public access - no auth required
    protected function authorizationRequired(): bool
    {
        return false;
    }
}