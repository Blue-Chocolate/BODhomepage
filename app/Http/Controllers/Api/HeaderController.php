<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HeaderSettingResource;
use App\Http\Resources\NavItemResource;
use App\Models\HeaderSetting;
use App\Models\NavItem;

class HeaderController extends Controller
{
    /**
     * GET /api/header
     * كل بيانات الهيدر في call واحدة
     */
    public function index()
    {
        return response()->json([
            'settings' => new HeaderSettingResource(HeaderSetting::getInstance()),
            'nav'      => NavItemResource::collection(NavItem::getMenu()),
        ]);
    }

    /**
     * GET /api/header/settings
     */
    public function settings()
    {
        return new HeaderSettingResource(HeaderSetting::getInstance());
    }

    /**
     * GET /api/header/nav
     */
    public function nav()
    {
        return NavItemResource::collection(NavItem::getMenu());
    }
}