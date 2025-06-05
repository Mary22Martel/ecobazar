<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RepartidorController;
use App\Http\Controllers\AgricultorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\Auth\AgricultorRegisterController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CanastaController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\Admin\MercadoController;
use App\Http\Controllers\Admin\UsuarioController;


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
Route::get('/progreso', function () {
    return view('progreso');
})->name('progreso');
Route::get('/nosotros', function () {
    return view('nosotros');
})->name('nosotros');




Route::get('/auth-check', function () {
    return response()->json(['authenticated' => Auth::check()]);
})->name('auth.check');

//Login y Register para Agricultor
Route::get('/agricultor/register', [AgricultorRegisterController::class, 'showRegistrationForm'])->name('agricultor.register');
Route::post('/agricultor/register', [AgricultorRegisterController::class, 'register'])->name('agricultor.register.submit');


// Listado público de ferias
Route::get('/mercados', [ProductoController::class, 'listadoMercados'])
     ->name('mercados.index');

// Catálogo filtrado por feria
Route::get('/mercados/{mercado}/tienda', [ProductoController::class, 'tiendaPorMercado'])
     ->name('mercados.tienda');


Auth::routes();

Route::middleware(['auth'])->group(function () {

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


Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    Route::resource('usuarios', UsuarioController::class)
         ->only(['index','edit','update']);
    
    // Rutas para canastas
    Route::resource('canastas', CanastaController::class);
    Route::resource('mercados', MercadoController::class);

    // Rutas para pedidos (nota: sin repetir 'admin/')
    //Route::get('/pedidos', [OrderController::class, 'todosLosPedidos'])->name('pedidos.index');
    Route::get('/pedido/{id}', [OrderController::class, 'detallePedidoAdmin'])->name('pedido.detalle');
   // Route::get('/pedidos', [AdminController::class, 'todosLosPedidos'])->name('pedidos.index');

    Route::post('/pedido/{id}/actualizar-estado', [OrderController::class, 'actualizarEstado'])->name('pedido.actualizar_estado');

});


// Rutas del carrito
Route::middleware(['auth'])->group(function () {
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/agregar/{productId}', [CarritoController::class, 'add'])->name('carrito.add');
    Route::post('/carrito/eliminar/{itemId}', [CarritoController::class, 'remove'])->name('carrito.remove');
    Route::post('/carrito/actualizar/{itemId}', [CarritoController::class, 'update'])->name('carrito.update');
   // Route::post('/carrito/agregar/{id}', [CarritoController::class, 'add'])->name('carrito.add');
    Route::get('/carrito/details', [CarritoController::class, 'getDetails'])->name('carrito.getDetails');
    Route::get('/checkout', [CarritoController::class, 'checkout'])->name('checkout');
    Route::get('/carrito/checkout', [CarritoController::class, 'checkout'])->name('carrito.checkout');
    Route::get('/carrito/load-data', [CarritoController::class, 'loadCartData'])->name('carrito.loadCartData');


});

Route::middleware(['auth'])->group(function () {
    // Rutas para realizar una orden y mostrar el éxito
    Route::post('/orden', [OrderController::class, 'store'])->name('order.store');
    Route::get('/orden-exito/{orderId}', [OrderController::class, 'success'])->name('order.success');
    Route::get('/orden-voucher/{orderId}', [OrderController::class, 'downloadVoucher'])->name('order.voucher');
    Route::get('/order/failed', [OrderController::class, 'failed'])->name('order.failed');
    Route::post('/mercadopago/webhook', [OrderController::class, 'mercadoPagoWebhook']);
    
    // Rutas para los agricultores relacionadas con pedidos
    Route::get('/agricultor/pedidos-pendientes', [OrderController::class, 'mostrarPedidosPendientes'])->name('agricultor.pedidos_pendientes');
    Route::get('/agricultor/pedido/{id}', [OrderController::class, 'detallePedido'])->name('agricultor.pedido.detalle');
    Route::post('/agricultor/pedido/{id}/confirmar-listo', [OrderController::class, 'confirmarPedidoListo'])->name('agricultor.confirmar_pedido_listo');
    Route::get('/agricultor/pedidos-listos', [OrderController::class, 'pedidosListos'])->name('agricultor.pedidos_listos');
    Route::get('/agricultor/pagos', [OrderController::class, 'pagosProductor'])
         ->name('agricultor.pagos');
});

//mercado pago
Route::post('/create-preference', [MercadoPagoController::class, 'createPaymentPreference']);
Route::get('/mercadopago/success', [MercadoPagoController::class, 'success'])->name('mercadopago.success');
Route::get('/mercadopago/failed', [MercadoPagoController::class, 'failed'])->name('mercadopago.failed');
Route::get('/order/success/{orderId}', [OrderController::class, 'success'])->name('order.id');

//repartidor
Route::middleware(['auth'])->group(function () {
    Route::get('/repartidor/pedidos-pendientes', [RepartidorController::class, 'pedidosPendientes'])
        ->name('repartidor.pedidos_pendientes');
    Route::post('/repartidor/pedido/{id}/entregado', [RepartidorController::class, 'marcarComoEntregado'])->name('repartidor.pedido.entregado');
    Route::post('/repartidor/pedido/{id}/proceso', [RepartidorController::class, 'marcarEnProceso'])->name('repartidor.pedido.proceso');
    

    Route::get('/repartidor/pedido/{id}', [RepartidorController::class, 'detallePedido'])->name('repartidor.pedido.detalle');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/resumen-pagos-agricultores', [OrderController::class, 'resumenPagosAgricultores'])->name('admin.resumen_pagos');
    Route::get('/admin/reporte-pagos-periodo', [OrderController::class, 'reportePagosPeriodo'])->name('admin.reporte_pagos');
    Route::post('/admin/pago-masivo-agricultores', [OrderController::class, 'pagoMasivoAgricultores'])->name('admin.pago_masivo');
});