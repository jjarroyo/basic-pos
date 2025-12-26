<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HybridAuthService
{
    protected $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Attempt to authenticate user (hybrid mode)
     * 
     * @param string $email
     * @param string $password
     * @param bool $remember
     * @return bool
     */
    public function attempt(string $email, string $password, bool $remember = false): bool
    {
        // Try local authentication first (faster and works offline)
        $localAuth = $this->attemptLocal($email, $password, $remember);
        
        if ($localAuth) {
            // If in client mode and server is online, sync user data in background
            if (config('pos.mode') === 'client' && $this->syncService->isServerOnline()) {
                $this->syncUserInBackground($email);
            }
            
            return true;
        }

        // If local auth failed and we're in client mode, try server
        if (config('pos.mode') === 'client') {
            return $this->attemptServer($email, $password, $remember);
        }

        return false;
    }

    /**
     * Attempt local authentication
     */
    protected function attemptLocal(string $email, string $password, bool $remember): bool
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return false;
        }

        if (!Hash::check($password, $user->password)) {
            return false;
        }

        // Login successful
        Auth::login($user, $remember);
        
        Log::info('Local authentication successful for: ' . $email);
        
        return true;
    }

    /**
     * Attempt server authentication and cache user locally
     */
    protected function attemptServer(string $email, string $password, bool $remember): bool
    {
        try {
            $serverUrl = 'http://' . config('pos.server_ip') . ':' . config('pos.server_port');
            
            $response = Http::timeout(5)->post($serverUrl . '/api/auth/login', [
                'email' => $email,
                'password' => $password,
            ]);

            if (!$response->successful()) {
                Log::warning('Server authentication failed for: ' . $email);
                return false;
            }

            $userData = $response->json();

            if (!$userData['success']) {
                return false;
            }

            // Create or update user locally
            $user = $this->cacheUserLocally($userData['user'], $password);

            if (!$user) {
                return false;
            }

            // Login with the local user
            Auth::login($user, $remember);
            
            Log::info('Server authentication successful and user cached: ' . $email);
            
            return true;

        } catch (\Exception $e) {
            Log::error('Server authentication error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cache user locally after successful server authentication
     */
    protected function cacheUserLocally(array $userData, string $password): ?User
    {
        try {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($password), // Store hashed password for offline login
                    'synced_at' => now(),
                ]
            );

            // Assign role if provided
            if (isset($userData['role'])) {
                if (!$user->hasRole($userData['role'])) {
                    $user->syncRoles([$userData['role']]);
                }
            }

            return $user;

        } catch (\Exception $e) {
            Log::error('Error caching user locally: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sync user data in background (non-blocking)
     */
    protected function syncUserInBackground(string $email): void
    {
        // This could be dispatched as a job in production
        // For now, we'll just log it
        Log::info('Background sync triggered for user: ' . $email);
    }

    /**
     * Check if user exists locally
     */
    public function userExistsLocally(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    /**
     * Get authentication mode for display
     */
    public function getAuthMode(): string
    {
        $mode = config('pos.mode');
        
        if ($mode === 'client') {
            return $this->syncService->isServerOnline() ? 'Online' : 'Offline';
        }
        
        return ucfirst($mode);
    }
}
