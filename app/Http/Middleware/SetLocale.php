<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // dd($locale = App::getLocale());
        // dd(session()->get('locale'));
        if (session()->has('locale')) {
            App::setLocale(session()->get('locale'));
        }

        /* if (session()->has('locale')) {
            $localeFromSession = session()->get('locale');
            App::setLocale($localeFromSession);

            // セッションから取得したロケールと、実際に設定されたロケールを確認
            dd('Middleware active:', [
                'session_locale' => $localeFromSession,
                'app_locale_after_set' => App::getLocale(),
                'request_path' => $request->path() // どのパスで dd が出たか確認
            ]);
        } else {
            // セッションにロケールがない場合
            dd('Middleware active but no locale in session.', [
                'current_app_locale' => App::getLocale(),
                'request_path' => $request->path()
            ]);
        }*/

        return $next($request);
    }
}
