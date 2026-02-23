<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request, CartService $cart)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Prevent 'cliente_presencial' from logging into the web platform
            if ($user->hasRole('cliente_presencial')) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Esta cuenta es de uso presencial y no tiene acceso a la plataforma web.',
                ]);
            }

            $request->session()->regenerate();

            // Sync guest cart to user account
            $cart->syncSessionToDb();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request, CartService $cart)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 1. Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(), // Auto-verify on public registration
            'client_type' => 'virtual',
        ]);

        // 2. Assign 'cliente_virtual' role automatically.
        $virtualRole = Role::firstOrCreate(['name' => 'cliente_virtual', 'guard_name' => 'web']);
        $user->assignRole($virtualRole);

        // 3. Create linked Client record for POS purchase history
        Client::firstOrCreate(
            ['email' => $user->email],
            [
                'user_id' => $user->id,
                'name' => $user->name,
                'document_type' => 'DNI',
                'document_number' => '00000000',
                'phone' => '000000000',
                'address' => 'Dirección por actualizar',
                'client_type' => 'virtual',
            ]
        );

        // 4. Fire Registered event (for future email verification hooks, etc.)
        event(new Registered($user));

        // 5. Log the user in
        Auth::login($user);

        // 6. Sync guest cart to user account
        $cart->syncSessionToDb();

        return redirect()->route('dashboard')
            ->with('success', '¡Bienvenido a StyleBox! Tu cuenta ha sido creada.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
