<?php

use App\Http\Controllers\AdminPedidoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardStatsController;
use App\Http\Controllers\MeasurementUnitController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductoTallaController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// 1. Authentication Routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// 2. Root Route (Public Catalog — no auth required)
Route::get('/', [CatalogoController::class, 'index'])->name('home');
Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo');

// 2.1 Public Shop Routes (TikTok-style feed)
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/{product}', [ShopController::class, 'show'])->name('show');
});

// 3. Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Shared Dashboard (Logic inside controller handles role redirection)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notificaciones dinámicas
    Route::get('/api/notifications', [\App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('api.notifications');

    // ── Shopping Cart ──────────────────────────────────────────────────────
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

    // ── Checkout ───────────────────────────────────────────────────────────
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{sale}/confirmacion', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
    Route::get('/checkout/{sale}/boleta', [CheckoutController::class, 'boleta'])->name('checkout.boleta');

    // ── Purchase History ──────────────────────────────────────────────────
    Route::get('/historial', [\App\Http\Controllers\HistorialController::class, 'index'])->name('historial.index');
    Route::get('/historial/{sale}', [\App\Http\Controllers\HistorialController::class, 'show'])->name('historial.show');
    Route::post('/historial/{sale}/repetir', [\App\Http\Controllers\HistorialController::class, 'repeatOrder'])->name('historial.repeat');

    // --- Admin & Staff Routes (Protected by Roles/Permissions ideally) ---
    Route::middleware(['role:admin|vendedor'])->group(function () {

        // POS System
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('/pos', [PosController::class, 'store'])->name('pos.store');

        // Management Resources
        Route::resource('products', ProductController::class);
        Route::resource('measurement_units', MeasurementUnitController::class);
        Route::resource('clients', ClientController::class);

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        // Seller Stats (Shared with Admin)
        Route::get('/seller/stats', [DashboardStatsController::class, 'getSellerStats'])->name('vendedor.stats');

        // ── Order Status Management ──────────────────────────────────────
        Route::post('/admin/orders/{sale}/estado', [CheckoutController::class, 'updateEstado'])->name('orders.estado');

        // ── Gestión de Tallas ──────────────────────────────────────────────
        Route::prefix('products/{product}/tallas')->name('product.tallas.')->group(function () {
            Route::get('/json', [ProductoTallaController::class, 'json'])->name('json');
            Route::post('/', [ProductoTallaController::class, 'store'])->name('store');
            Route::put('/{producto_talla}', [ProductoTallaController::class, 'update'])->name('update');
            Route::delete('/{producto_talla}', [ProductoTallaController::class, 'destroy'])->name('destroy');
        });
        Route::get('/inventario/tallas', [ProductoTallaController::class, 'inventario'])->name('inventario.tallas');
    });


    // --- System Administration (Admin Only) ---
    Route::middleware(['role:admin'])->name('admin.')->group(function () {
        Route::get('/admin/stats', [DashboardStatsController::class, 'getAdminStats'])->name('stats');

        // Users — protected by usuarios.manage permission
        Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('permission:usuarios.manage');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:usuarios.manage');
        Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('permission:usuarios.manage');
        Route::get('users/{user}/roles', [UserController::class, 'assignRoles'])->name('users.roles')->middleware('permission:usuarios.manage');
        Route::post('users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.roles.update')->middleware('permission:usuarios.manage');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:usuarios.manage');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:usuarios.manage');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:usuarios.manage');
        Route::patch('users/{user}', [UserController::class, 'update'])->middleware('permission:usuarios.manage');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:usuarios.manage');

        // Roles — protected by roles.manage permission
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:roles.manage');
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:roles.manage');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:roles.manage');
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show')->middleware('permission:roles.manage');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:roles.manage');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:roles.manage');
        Route::patch('roles/{role}', [RoleController::class, 'update'])->middleware('permission:roles.manage');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles.manage');

        // Permissions — admin only (already protected by role:admin group)
        Route::resource('permissions', PermissionController::class);

        // ── Pedidos Virtuales ────────────────────────────────
        Route::get('/pedidos-virtuales', [AdminPedidoController::class, 'index'])->name('pedidos.index')->middleware('permission:pedidos.virtuales.manage');
        Route::get('/pedidos-virtuales/{pedido}', [AdminPedidoController::class, 'show'])->name('pedidos.show')->middleware('permission:pedidos.virtuales.manage');
        Route::post('/pedidos-virtuales/{pedido}/confirmar', [AdminPedidoController::class, 'confirmarPago'])->name('pedidos.confirmar')->middleware('permission:pedidos.virtuales.manage');
        Route::patch('/pedidos-virtuales/{pedido}/estado', [AdminPedidoController::class, 'cambiarEstado'])->name('pedidos.estado.update')->middleware('permission:pedidos.virtuales.manage');
    });

});