<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConvertRedirectToJson
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->expectsJson() && $response instanceof RedirectResponse) {
            $session = $request->session();
            $type = 'success';
            $message = 'Done!';

            if ($session->has('error')) {
                $type = 'error';
                $message = $session->get('error');
            } elseif ($session->has('warning')) {
                $type = 'warning';
                $message = $session->get('warning');
            } elseif ($session->has('info')) {
                $type = 'info';
                $message = $session->get('info');
            } elseif ($session->has('success')) {
                $message = $session->get('success');
            }

            $data = [
                'message' => $message,
                'type' => $type,
                'redirect' => $response->getTargetUrl(),
            ];

            if ($session->has('errors')) {
                $data['errors'] = $session->get('errors')->getBag('default')->all();
                $data['type'] = 'error';
            }

            return response()->json($data);
        }

        return $response;
    }
}
