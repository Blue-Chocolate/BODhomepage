<?php

// app/Http/Controllers/Api/WhoAreWeController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WhoAreWeResource;
use App\Models\WhoAreWe;

class WhoAreWeController extends Controller
{
    public function index()
    {
        return new WhoAreWeResource(
            cache()->remember('who_are_we', 300, fn() => WhoAreWe::getInstance())
        );
    }
}
