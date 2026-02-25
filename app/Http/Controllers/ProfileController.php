<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        return view('profile.index', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $バランス)
    {
        $user = auth()->user();

        $バランス->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $user->name = $バランス->name;
        $user->email = $バランス->email;

        if ($バランス->filled('password')) {
            $user->password = Hash::make($バランス->password);
        }

        $user->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}
