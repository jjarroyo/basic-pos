<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSetupCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('logout') || $request->routeIs('logout')) {
            return $next($request);
        }

        $setupCompleted = Setting::get('setup_completed', false);
        
        if (!$setupCompleted && Auth::check() && Auth::user()->hasRole('admin')) {
            if (!$request->is('setup')) {
                return redirect()->route('setup');
            }
        }
        
        if ($setupCompleted && $request->is('setup')) {
            return redirect()->route('dashboard');
        }
        
        return $next($request);
    }
}
