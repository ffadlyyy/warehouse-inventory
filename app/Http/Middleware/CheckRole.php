<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Pastikan user sudah terautentikasi dan role-nya sesuai
        if (! $request->user() || $request->user()->role !== $role) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk aksi ini.');
        }

        return $next($request);
    }
}