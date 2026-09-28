<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('jwt_token')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses dashboard.');
        }

        return $next($request);
    }
}
