<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Hubo un error al autenticar con ' . ucfirst($provider));
        }

        // Find or create user
        $user = User::where($provider . '_id', $socialUser->getId())
            ->orWhere('email', $socialUser->getEmail())
            ->first();

        if ($user) {
            // Update provider ID if not set
            if (!$user->{$provider . '_id'}) {
                $user->update([$provider . '_id' => $socialUser->getId()]);
            }
        } else {
            // Create new user
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Usuario Social',
                'email' => $socialUser->getEmail(),
                'password' => bcrypt(Str::random(16)),
                $provider . '_id' => $socialUser->getId(),
            ]);

            // Assign default role (e.g., cliente)
            if (isset($user->assignRole)) {
                $user->assignRole('cliente');
            }
        }

        Auth::login($user);

        return redirect()->route('home');
    }
}
