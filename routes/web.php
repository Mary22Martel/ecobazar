<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RepartidorController;
use App\Http\Controllers\AgricultorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\Auth\AgricultorRegisterController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MedidaController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CanastaController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MercadoPagoController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index'])->name('homepage');
Route::get('/tienda', [ProductoController::class, 'tienda'])->name('tienda');
Route::get('/buscar-productos', [ProductoController::class, 'buscarProductos'])->name('buscar.productos');
Route::get('/productos/categoria/{categoria}', [ProductoController::class, 'filtrarPorCategoria'])->name('productos.filtrarPorCategoria');
Route::get('/tienda/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');
Route::get('/producto/{id}', [ProductoController::class, 'show'])->name('producto.show');
Route::get('/productos/filtrar-precio', [ProductoController::class, 'filtrarPorPrecio'])->name('productos.filtrarPorPrecio');
Route::get('/productos/productor/{id}', [ProductoController::class, 'filtrarPorProductor'])->name('productos.filtrarPorProductor');
Route::get('/buscar-productos/ajax', [ProductoController::class, 'buscarProductosAjax'])->name('buscar.productos.ajax');


Route::get('/auth-check', function () {
    return response()->json(['authenticated' => auth()->check()]);
})->name('auth.check');



Auth::routes();

Route::middleware(['auth'])->group(function () {
    
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/repartidor', [RepartidorController::class, 'index'])->name('repartidor.dashboard');
    Route::get('/agricultor', [AgricultorController::class, 'index'])->name('agricultor.dashboard');
    Route::get('/cliente', [ClienteController::class, 'index'])->name('cliente.dashboard');
});

//Agricultor - Productos
Route::middleware(['auth'])->group(function () {
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

});

// Grupo de rutas para la administración de categorías y medidas (solo admin)
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    // Rutas para categorías
    Route::resource('categorias', CategoriaController::class);
    
    // Rutas para medidas
    Route::resource('medidas', MedidaController::class);

    // Rutas para canastas
    Route::resource('canastas', CanastaController::class);

    // Rutas para pedidos (nota: sin repetir 'admin/')
    Route::get('/pedidos', [OrderController::class, 'todosLosPedidos'])->name('pedidos.index');
    Route::get('/pedido/{id}', [OrderController::class, 'detallePedidoAdmin'])->name('pedido.detalle');
    Route::post('/pedido/{id}/actualizar-estado', [OrderController::class, 'actualizarEstado'])->name('pedido.actualizar_estado');
});


//Login y Register para Agricultor
Route::get('/agricultor/register', [AgricultorRegisterController::class, 'showRegistrationForm'])->name('agricultor.register');
Route::post('/agricultor/register', [AgricultorRegisterController::class, 'register'])->name('agricultor.register.submit');


// Rutas del carrito
Route::middleware(['auth'])->group(function () {
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/agregar/{productId}', [CarritoController::class, 'add'])->name('carrito.add');
    Route::post('/carrito/eliminar/{itemId}', [CarritoController::class, 'remove'])->name('carrito.remove');
    Route::post('/carrito/actualizar/{itemId}', [CarritoController::class, 'update'])->name('carrito.update');
   // Route::post('/carrito/agregar/{id}', [CarritoController::class, 'add'])->name('carrito.add');
    Route::get('/carrito/details', [CarritoController::class, 'getDetails'])->name('carrito.getDetails');
    Route::get('/checkout', [CarritoController::class, 'checkout'])->name('checkout');

});

Route::middleware(['auth'])->group(function () {
    // Rutas para realizar una orden y mostrar el éxito
    Route::post('/orden', [OrderController::class, 'store'])->name('order.store');
    Route::get('/orden-exito/{orderId}', [OrderController::class, 'success'])->name('order.success');
    Route::get('/orden-voucher/{orderId}', [OrderController::class, 'downloadVoucher'])->name('order.voucher');

    // Rutas para los agricultores relacionadas con pedidos
    Route::get('/agricultor/pedidos-pendientes', [OrderController::class, 'mostrarPedidosPendientes'])->name('agricultor.pedidos_pendientes');
    Route::get('/agricultor/pedido/{id}', [OrderController::class, 'detallePedido'])->name('agricultor.pedido.detalle');
    Route::post('/agricultor/pedido/{id}/confirmar-listo', [OrderController::class, 'confirmarPedidoListo'])->name('agricultor.confirmar_pedido_listo');
    Route::get('/agricultor/pedidos-listos', [OrderController::class, 'pedidosListos'])->name('agricultor.pedidos_listos');
});

//mercado pago
Route::post('/create-preference', [MercadoPagoController::class, 'createPaymentPreference']);
Route::get('/mercadopago/success', [MercadoPagoController::class, 'success'])->name('mercadopago.success');
Route::get('/mercadopago/failed', [MercadoPagoController::class, 'failed'])->name('mercadopago.failed');
Route::get('/order/success/{orderId}', [OrderController::class, 'success'])->name('order.id');




