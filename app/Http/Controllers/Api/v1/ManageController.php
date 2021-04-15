<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Trade;

class ManageController extends Controller
{
    public function index()
    {
        $setting = Setting::find(1);
        return $setting;
    }



    public function active()
    {
        $setting = Setting::active();
        return $setting->active;
    }

    public function inActive()
    {
        $setting = Setting::inActive();
        return $setting->active;
    }



    public function swapActive()
    {
        $setting = Setting::swapActive();
        return $setting->started_swap;
    }

    public function swapInActive()
    {
        $setting = Setting::swapInActive();
        return $setting->started_swap;
    }

}
