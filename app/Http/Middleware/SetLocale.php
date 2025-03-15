<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is changing language
        if ($request->has('language') && in_array($request->language, ['ms', 'en'])) {
            Session::put('language', $request->language);

            // Update user preference if logged in
            if (Auth::check()) {
                Auth::user()->update(['language' => $request->language]);
            }
        }

        // Set locale from session, user preference, or default to Bahasa Malaysia
        $locale = Session::get('language', Auth::check() ? Auth::user()->language : 'ms');
        App::setLocale($locale);

        return $next($request);
    }
}
