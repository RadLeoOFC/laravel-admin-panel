<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AuthService
{
    public function login($credentials)
    {
        if (!Auth::attempt($credentials)) {
            return false;
        }

        session()->regenerate();
        return true;
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    public function resetPassword($data)
    {
        return Password::reset(
            $data,
            function ($user, $password) {
                $user->update(['password' => bcrypt($password)]);
            }
        );
    }
}
