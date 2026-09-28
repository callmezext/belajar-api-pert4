<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('jwt_token')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = session('user');
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Halaman ini khusus Administrator.');
        }

        return $next($request);
    }
}
