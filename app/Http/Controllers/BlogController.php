<?php

namespace App\Http\Controllers;

use Orion\Http\Controllers\Controller as ApiController;

class BlogController extends ApiController
{
    protected $model = \App\Models\Blog::class;
}