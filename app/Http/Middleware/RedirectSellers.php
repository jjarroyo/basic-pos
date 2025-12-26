<?php

namespace App\Http\Middleware;

use App\Models\CashRegisterSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectSellers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicar a usuarios autenticados con rol 'seller'
        if (auth()->check() && auth()->user()->hasRole('seller')) {
            
            // Verificar si tiene una sesión de caja abierta
            $openSession = CashRegisterSession::where('user_id', auth()->id())
                ->where('status', 'open')
                ->first();

            // Rutas permitidas para sellers (POS, caja, y autenticación)
            $allowedRoutes = [
                'pos', 
                'cash.open', 
                'cash.close',
                'login',
                'logout',
                'register',
                'password.request',
                'password.email',
                'password.reset',
                'password.update',
                'verification.notice',
                'verification.verify',
                'verification.send',
            ];
            
            // Si está intentando acceder a una ruta no permitida
            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                // Si tiene caja abierta, redirigir al POS
                if ($openSession) {
                    return redirect()->route('pos');
                }
                // Si no tiene caja abierta, redirigir a abrir caja
                return redirect()->route('cash.open');
            }
        }

        return $next($request);
    }
}
