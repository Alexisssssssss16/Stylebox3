<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─────────────────────────────────────────────────────────
        // PERMISSIONS — organized by functional module (dot notation)
        // ─────────────────────────────────────────────────────────

        $permissions = [

            // ── Productos ────────────────────────────────────────
            'productos.view',
            'productos.create',
            'productos.edit',
            'productos.delete',

            // ── Ventas / POS ─────────────────────────────────────
            'ventas.view',
            'ventas.create',
            'ventas.edit',
            'ventas.delete',

            // ── Clientes ─────────────────────────────────────────
            'clientes.view',
            'clientes.create',
            'clientes.edit',
            'clientes.delete',

            // ── Inventario ───────────────────────────────────────
            'inventario.manage',

            // ── Reportes ─────────────────────────────────────────
            'reportes.view',

            // ── Unidades de Medida ───────────────────────────────
            'medidas.manage',

            // ── Usuarios del Sistema ─────────────────────────────
            'usuarios.manage',

            // ── Roles del Sistema ────────────────────────────────
            'roles.manage',

            // ── Cliente Virtual (antes comprador) ───────────────
            'cliente_virtual.catalogo',
            'cliente_virtual.carrito',
            'cliente_virtual.checkout',
            'cliente_virtual.pedidos',
            'cliente_virtual.perfil',

            // ── Cliente Presencial ──────────────────────────────
            'cliente_presencial.pedidos',

            // ── Pedidos Virtuales (Admin) ────────────────────────
            'pedidos.virtuales.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ─────────────────────────────────────────────────────────
        // ROLES — with functional permission sets
        // ─────────────────────────────────────────────────────────

        // 1. Admin — control total del sistema
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleAdmin->syncPermissions(Permission::all());

        // 2. Vendedor — gestión operativa de tienda
        $roleVendedor = Role::firstOrCreate(['name' => 'vendedor']);
        $roleVendedor->syncPermissions([
            'productos.view',
            'ventas.view',
            'ventas.create',
            'ventas.edit',
            'clientes.view',
            'clientes.create',
            'clientes.edit',
            'inventario.manage',
            'reportes.view',
            'medidas.manage',
        ]);

        // 3. Cliente Virtual — registrado vía web
        $roleVirtual = Role::firstOrCreate(['name' => 'cliente_virtual']);
        // Migrar permisos de 'comprador' si existía
        $roleVirtual->syncPermissions([
            'cliente_virtual.catalogo',
            'cliente_virtual.carrito',
            'cliente_virtual.checkout',
            'cliente_virtual.pedidos',
            'cliente_virtual.perfil',
        ]);

        // 4. Cliente Presencial — registrado en tienda
        $rolePresencial = Role::firstOrCreate(['name' => 'cliente_presencial']);
        $rolePresencial->syncPermissions([
            'cliente_presencial.pedidos',
        ]);

        // ─────────────────────────────────────────────────────────
        // DEMO USERS
        // ─────────────────────────────────────────────────────────

        // Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@stylebox.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole($roleAdmin);
        }

        // Vendedor demo
        $vendedor = User::firstOrCreate(
            ['email' => 'vendedor@stylebox.com'],
            [
                'name' => 'Vendedor Demo',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        if (!$vendedor->hasRole('vendedor')) {
            $vendedor->assignRole($roleVendedor);
        }

        // Cliente Virtual demo
        $clienteVirtual = User::firstOrCreate(
            ['email' => 'cliente@stylebox.com'],
            [
                'name' => 'Cliente Virtual',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'client_type' => 'virtual',
            ]
        );
        if (!$clienteVirtual->hasRole('cliente_virtual')) {
            $clienteVirtual->assignRole($roleVirtual);
        }

        // Create Client record for Virtual Demo
        Client::firstOrCreate(
            ['email' => $clienteVirtual->email],
            [
                'user_id' => $clienteVirtual->id,
                'name' => $clienteVirtual->name,
                'client_type' => 'virtual',
                'status' => true,
                'document_type' => 'DNI',
                'document_number' => '11111111',
            ]
        );

        // Cliente Presencial demo (not able to login normally, but exists for history)
        $clientePresencial = User::firstOrCreate(
            ['email' => 'presencial@stylebox.com'],
            [
                'name' => 'Cliente Presencial',
                'password' => Hash::make('nopassword-store-only'),
                'email_verified_at' => now(),
                'client_type' => 'presencial',
            ]
        );
        if (!$clientePresencial->hasRole('cliente_presencial')) {
            $clientePresencial->assignRole($rolePresencial);
        }

        // Create Client record for Presencial Demo
        Client::firstOrCreate(
            ['email' => $clientePresencial->email],
            [
                'user_id' => $clientePresencial->id,
                'name' => $clientePresencial->name,
                'client_type' => 'presencial',
                'status' => true,
                'document_type' => 'DNI',
                'document_number' => '22222222',
            ]
        );

        // ─────────────────────────────────────────────────────────
        // OUTPUT SUMMARY
        // ─────────────────────────────────────────────────────────

        $this->command->info('');
        $this->command->info('✅ Roles y permisos creados correctamente.');
        $this->command->info('');
        $this->command->table(
            ['Rol', 'Permisos asignados', 'Email demo', 'Contraseña'],
            [
                ['admin', Permission::all()->count() . ' (todos)', 'admin@stylebox.com', 'password'],
                ['vendedor', '10', 'vendedor@stylebox.com', 'password'],
                ['cliente_virtual', '5', 'cliente@stylebox.com', 'password'],
                ['cliente_presencial', '1', 'presencial@stylebox.com', 'N/A'],
            ]
        );
    }
}
