<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sessionKey = config('localization.session_key', 'locale');
        $supported = config('localization.supported', ['en']);
        $locale = session($sessionKey, config('app.locale', 'en'));

        if (! in_array($locale, $supported, true)) {
            $locale = config('app.locale', 'en');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
