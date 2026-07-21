<?php
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ComboController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RecetaController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware(['auth', 'verified']);

Route::middleware('auth')->group(function () {
    // Perfil: cualquier usuario autenticado
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Ver catálogo: Administrador, Cajero y Cocinero ---
    Route::middleware('role:Administrador|Cajero|Cocinero')->group(function () {
        Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
        Route::resource('usuarios', UsuarioController::class)->except(['show'])->parameters(['usuarios' => 'usuario']);
    });

    // --- Gestión de catálogo (crear/editar/borrar): Administrador y Cocinero ---
    Route::middleware('role:Administrador|Cocinero')->group(function () {
        Route::get('/productos/crear', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/productos/{producto}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
        Route::put('/inventario/{inventario}/minimo', [InventarioController::class, 'updateMinimo'])->name('inventario.minimo');

        // Compras y proveedores
        Route::resource('proveedores', ProveedorController::class)->except(['show']);
        Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
        Route::get('/compras/crear', [CompraController::class, 'create'])->name('compras.create');
        Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');
        Route::get('/recetas', [RecetaController::class, 'index'])->name('recetas.index');
        Route::get('/recetas/{producto}/editar', [RecetaController::class, 'edit'])->name('recetas.edit');
        Route::put('/recetas/{producto}', [RecetaController::class, 'update'])->name('recetas.update');
        Route::get('/produccion', [RecetaController::class, 'produccion'])->name('produccion.index');
        Route::post('/produccion', [RecetaController::class, 'producir'])->name('produccion.producir');
    });

    // --- Ventas y caja: Administrador y Cajero ---
    Route::middleware('role:Administrador|Cajero')->group(function () {
        Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
        Route::get('/ventas/crear', [VentaController::class, 'create'])->name('ventas.create');
        Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
        Route::put('/ventas/{venta}/cancelar', [VentaController::class, 'cancelar'])->name('ventas.cancelar');
        Route::get('/ventas/{venta}/recibo', [VentaController::class, 'recibo'])->name('ventas.recibo');

        Route::get('/caja', [CajaController::class, 'index'])->name('caja.index');
        Route::post('/caja/abrir', [CajaController::class, 'abrir'])->name('caja.abrir');
        Route::put('/caja/{caja}/cerrar', [CajaController::class, 'cerrar'])->name('caja.cerrar');

        // Clientes y combos (se usan al vender)
        Route::resource('clientes', ClienteController::class)->except(['show']);
        Route::resource('combos', ComboController::class)->only(['index', 'create', 'store', 'destroy']);
    });

    // --- Reportes: solo Administrador ---
    Route::middleware('role:Administrador')->group(function () {
        Route::get('/reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
        Route::get('/reportes/ventas/pdf', [ReporteController::class, 'ventasPdf'])->name('reportes.ventas.pdf');
    });
});

require __DIR__.'/auth.php';