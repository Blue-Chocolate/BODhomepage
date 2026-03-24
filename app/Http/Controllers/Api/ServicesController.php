<?php

// app/Http/Controllers/Api/ServicesController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceSectionResource;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Models\ServiceSectionSetting;

class ServicesController extends Controller
{
    public function index()
    {
        return new ServiceSectionResource(
            cache()->remember('services_section', 300, fn() => ServiceSectionSetting::getInstance())
        );
    }

    public function show(Service $service)
    {
        return new ServiceResource($service);
    }
}