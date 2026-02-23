<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Group permissions by their module prefix (before the dot).
     */
    private function groupPermissions($permissions): array
    {
        $grouped = [];
        foreach ($permissions as $perm) {
            $parts = explode('.', $perm->name, 2);
            $module = $parts[0] ?? 'otros';
            $action = $parts[1] ?? $perm->name;
            $grouped[$module][] = $action;
        }
        return $grouped;
    }

    public function index(Request $request)
    {
        $roles = Role::with(['permissions', 'users'])->orderBy('id')->paginate(20);
        $roleConfig = config('roles', []);

        return view('roles.index', compact('roles', 'roleConfig'));
    }

    public function create()
    {
        $allPermissions = Permission::orderBy('name')->get();
        $grouped = $this->groupPermissions($allPermissions);
        return view('roles.create', ['grouped' => $grouped, 'allPermissions' => $allPermissions]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permission' => 'required|array',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol creado exitosamente.');
    }

    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $grouped = $this->groupPermissions($role->permissions);
        $roleConfig = config('roles', []);
        $cfg = $roleConfig[$role->name] ?? null;

        return view('roles.show', compact('role', 'grouped', 'cfg'));
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $allPermissions = Permission::orderBy('name')->get();
        $grouped = $this->groupPermissions($allPermissions);
        $rolePermissions = DB::table('role_has_permissions')
            ->where('role_id', $id)
            ->pluck('permission_id', 'permission_id')
            ->all();

        return view('roles.edit', compact('role', 'grouped', 'allPermissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'permission' => 'required|array',
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->input('name');
        $role->save();
        $role->syncPermissions($request->input('permission'));

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Prevent deleting system roles
        if (in_array($role->name, ['admin', 'vendedor', 'comprador'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', "No puedes eliminar el rol base «{$role->name}». Es parte del sistema.");
        }

        // Prevent deleting role with users assigned
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'No puedes eliminar este rol porque tiene usuarios asignados.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol eliminado exitosamente.');
    }
}
