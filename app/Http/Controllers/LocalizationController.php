<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LocalizationController extends Controller
{
    public function setLocale($locale)
    {
        if (! in_array($locale, config('app.locales'))) {
            $locale = config('app.fallback_locale');
        }

        Session::put('locale', $locale);
        App::setLocale($locale);
        //dd($currentLocale = App::getLocale());
        //dd($locale);
        return redirect()->back();
    }
}
