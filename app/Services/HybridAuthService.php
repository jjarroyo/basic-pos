<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class HybridAuthService
{
    /**
     * Attempt to authenticate user (local only)
     * 
     * @param string $email
     * @param string $password
     * @param bool $remember
     * @return bool
     */
    public function attempt(string $email, string $password, bool $remember = false): bool
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
        
        Log::info('Authentication successful for: ' . $email);
        
        return true;
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
        return 'Standalone';
    }
}
