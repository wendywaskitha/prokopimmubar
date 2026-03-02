<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilamentRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!$request->user()) {
            return redirect()->route('filament.admin.auth.login');
        }
        
        // Cek apakah user memiliki salah satu role yang diperlukan
        $hasRole = false;
        foreach ($roles as $role) {
            if ($request->user()->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }
        
        if (!$hasRole) {
            // Redirect ke dashboard dengan pesan error
            return redirect()->route('filament.admin.pages.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        return $next($request);
    }
}