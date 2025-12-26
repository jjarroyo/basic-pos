<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\HybridAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $hybridAuth;

    public function __construct(HybridAuthService $hybridAuth)
    {
        $this->hybridAuth = $hybridAuth;
    }

    /**
     * Handle login request
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Rate limiting
        $this->ensureIsNotRateLimited($request);

        // Attempt hybrid authentication
        $authenticated = $this->hybridAuth->attempt(
            $request->email,
            $request->password,
            $request->boolean('remember')
        );

        if (!$authenticated) {
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        $request->session()->regenerate();

        // Sincronizar datos si estamos en modo cliente
        if (config('pos.mode') === 'client') {
            $this->syncClientData();
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Sincronizar datos del servidor después del login
     */
    protected function syncClientData(): void
    {
        try {
            $syncService = app(\App\Services\SyncService::class);
            
            // Sincronizar datos básicos necesarios para operar
            $syncService->pullUsers();
            $syncService->pullCategories();
            $syncService->pullProducts();
            $syncService->pullClients();
            $syncService->pullCashRegisters();
            
            // La última sincronización se guarda automáticamente en cada método pull
            
            // Mensaje de éxito para el usuario
            session()->flash('sync_success', 'Datos sincronizados con el servidor exitosamente');
            
        } catch (\Exception $e) {
            // Log error pero no bloquear el login
            Log::warning('Failed to sync data after login: ' . $e->getMessage());
        }
    }

    /**
     * Ensure the login request is not rate limited
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key
     */
    protected function throttleKey(Request $request): string
    {
        return strtolower($request->input('email')) . '|' . $request->ip();
    }
}
