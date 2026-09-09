<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class HandlePageSplash
{
    /**
     * Decide whether the full-page splash loader should be shown for this
     * request. It renders only on the very first visit of a session, or
     * immediately after login/registration — never on normal in-app
     * navigation between pages/tabs.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->ajax()) {
            return $next($request);
        }

        $session = $request->session();

        $isFirstVisit = !$session->has('splash_seen');
        $justAuthed = (bool) $session->pull('splash_on_next');

        if ($isFirstVisit) {
            $session->put('splash_seen', true);
        }

        View::share('splashShow', $isFirstVisit || $justAuthed);

        return $next($request);
    }
}