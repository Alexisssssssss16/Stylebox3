<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Returns role config metadata, merging DB permissions count dynamically.
     */
    private function getRoleConfig(): array
    {
        $config = config('roles', []);

        // Merge live permission count from DB for each role
        foreach (Role::with('permissions')->get() as $role) {
            if (isset($config[$role->name])) {
                $config[$role->name]['permission_count'] = $role->permissions->count();
            }
        }

        return $config;
    }

    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            })
            ->paginate(10);

        return view('users.index', [
            'users' => $users,
            'roleConfig' => $this->getRoleConfig(),
        ]);
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('users.create', [
            'roles' => $roles,
            'roleConfig' => $this->getRoleConfig(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function show(User $user)
    {
        return view('users.show', [
            'user' => $user->load('roles.permissions'),
            'roleConfig' => $this->getRoleConfig(),
        ]);
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        return view('users.edit', [
            'user' => $user,
            'roles' => $roles,
            'roleConfig' => $this->getRoleConfig(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function assignRoles(User $user)
    {
        $roles = Role::orderBy('name')->get();
        return view('users.assign_roles', [
            'user' => $user,
            'roles' => $roles,
            'roleConfig' => $this->getRoleConfig(),
        ]);
    }

    public function updateRoles(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $newRole = $request->role;

        // Security: prevent removing 'admin' from last admin
        if ($user->hasRole('admin') && $newRole !== 'admin') {
            if (User::role('admin')->count() <= 1) {
                return back()->with('error', 'No puedes quitar el rol de administrador al último administrador del sistema.');
            }
        }

        $user->syncRoles([$newRole]);

        $roleConfig = config('roles', []);
        $roleLabel = $roleConfig[$newRole]['label'] ?? ucfirst($newRole);
        $roleModules = $roleConfig[$newRole]['modules'] ?? [];
        $modulesList = implode(', ', $roleModules);

        return redirect()->route('admin.users.index')
            ->with('success', "Rol de {$user->name} cambiado a {$roleLabel}. Ahora tiene acceso a: {$modulesList}.");
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('admin') && User::role('admin')->count() == 1) {
            return back()->with('error', 'No se puede eliminar el último administrador.');
        }

        $user->delete();
        return back()->with('success', 'Usuario eliminado correctamente.');
    }
}
