<?php

/**
 * StyleBox — Role Configuration
 *
 * Centralizes role metadata (label, icon, color, description, modules).
 * Used by views and controllers to avoid hardcoding role info in Blade files.
 */

return [

    'admin' => [
        'label' => 'Administrador',
        'icon' => 'fas fa-crown',
        'color_bg' => '#1a1a1a',
        'color_text' => '#d4a017',
        'badge_class' => 'bg-dark text-warning',
        'description' => 'Control total del sistema. Puede gestionar usuarios, roles, productos, ventas, inventario, reportes y configuración.',
        'modules' => [
            'Gestión de Usuarios',
            'Gestión de Roles',
            'Productos (CRUD completo)',
            'Inventario',
            'Ventas y POS',
            'Reportes globales',
            'Configuración del sistema',
        ],
    ],

    'vendedor' => [
        'label' => 'Vendedor',
        'icon' => 'fas fa-store',
        'color_bg' => '#198754',
        'color_text' => '#ffffff',
        'badge_class' => 'bg-success text-white',
        'description' => 'Gestión operativa de la tienda. Puede operar el POS, gestionar clientes, consultar inventario y ver sus reportes de ventas.',
        'modules' => [
            'POS (Punto de Venta)',
            'Crear y ver ventas',
            'Gestión de Clientes',
            'Consultar Productos',
            'Consultar Inventario',
            'Reportes de sus ventas',
            'Unidades de Medida',
        ],
    ],

    'comprador' => [
        'label' => 'Comprador',
        'icon' => 'fas fa-shopping-bag',
        'color_bg' => '#0d6efd',
        'color_text' => '#ffffff',
        'badge_class' => 'bg-primary text-white',
        'description' => 'Cliente registrado de la tienda. Puede navegar el catálogo, agregar productos al carrito, realizar compras y ver su historial de pedidos.',
        'modules' => [
            'Ver catálogo de productos',
            'Agregar al carrito',
            'Realizar checkout',
            'Historial de pedidos',
            'Editar su perfil',
        ],
    ],

];
