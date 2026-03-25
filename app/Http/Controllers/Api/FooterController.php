<?php

// app/Http/Controllers/Api/FooterController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FooterResource;
use App\Models\FooterSetting;

class FooterController extends Controller
{
    public function index()
    {
        return new FooterResource(
            cache()->remember('footer_data', 300, fn() => FooterSetting::getInstance())
        );
    }
}