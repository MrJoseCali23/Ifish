<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCriaderoIsSelected
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->rol === 'Dueño') {
            
            if (!session()->has('active_criadero_id') && !$request->routeIs('criaderos.select')) {
                
                return redirect()->route('criaderos.select');
            }
        }

        return $next($request);
    }
}