<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocalizationController extends Controller
{
    public function setLocale($locale)
    {
        if (! in_array($locale, config('app.locales'))) {
            $locale = config('app.fallback_locale');
        }

        Session::put('locale', $locale);
        App::setLocale($locale);

        // dd($currentLocale = App::getLocale());
        // dd($locale);
        return redirect()->back();
    }
}
