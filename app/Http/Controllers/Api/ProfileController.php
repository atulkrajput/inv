<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Setting;

class ProfileController extends Controller
{
    public function getData()
    {
        $settings = Setting::get();
        $setting = [];
        foreach($settings as $value):
            $setting[$value->name] = $value->value;
        endforeach;

        return $setting;
    }
}
