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
use App\Http\Controllers\AdminController;
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
    Route::resource('mercados', MercadoController::class);
    Route::get('/pedido/{id}', [OrderController::class, 'detallePedidoAdmin'])->name('pedido.detalle');
    Route::post('/pedido/{id}/actualizar-estado', [OrderController::class, 'actualizarEstado'])->name('pedido.actualizar_estado');

});


// Rutas del carrito
Route::middleware(['auth'])->group(function () {
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/agregar/{productId}', [CarritoController::class, 'add'])->name('carrito.add');
    Route::post('/carrito/eliminar/{itemId}', [CarritoController::class, 'remove'])->name('carrito.remove');
    Route::delete('/carrito/eliminar-ajax/{item}', [CarritoController::class, 'removeAjax'])->name('carrito.remove.ajax');
    Route::post('/carrito/clear', [CarritoController::class, 'clear'])->name('carrito.clear');
    Route::post('/carrito/actualizar/{itemId}', [CarritoController::class, 'update'])->name('carrito.update');
   // Route::post('/carrito/agregar/{id}', [CarritoController::class, 'add'])->name('carrito.add');
    Route::get('/carrito/details', [CarritoController::class, 'getDetails'])->name('carrito.getDetails');
    Route::get('/checkout', [CarritoController::class, 'checkout'])->name('checkout');
    Route::get('/carrito/checkout', [CarritoController::class, 'checkout'])->name('carrito.checkout');
    Route::get('/carrito/load-data', [CarritoController::class, 'loadCartData'])->name('carrito.loadCartData');
    Route::get('/carrito/count', [CarritoController::class, 'count'])->name('carrito.count');


});

Route::middleware(['auth'])->group(function () {
    // Rutas para realizar una orden y mostrar el éxito
    Route::post('/orden', [OrderController::class, 'store'])->name('order.store');
    Route::get('/orden-exito/{orderId}', [OrderController::class, 'success'])->name('order.success');
    Route::get('/orden-voucher/{orderId}', [OrderController::class, 'downloadVoucher'])->name('order.voucher');
    Route::get('/order/failed', [OrderController::class, 'failed'])->name('order.failed');
    Route::post('/mercadopago/webhook', [OrderController::class, 'mercadoPagoWebhook']);
    
    // Rutas para los agricultores relacionadas con pedidos
   // ==================== RUTAS PARA AGRICULTORES ====================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard del agricultor
    Route::get('/agricultor/dashboard', [AgricultorController::class, 'index'])->name('agricultor.dashboard');
    
    // ========== GESTIÓN DE PRODUCTOS ==========
    Route::get('/agricultor/productos', [AgricultorController::class, 'productos'])->name('agricultor.productos.index');
    Route::get('/agricultor/productos/crear', [AgricultorController::class, 'crearProducto'])->name('agricultor.productos.create');
    Route::post('/agricultor/productos', [AgricultorController::class, 'guardarProducto'])->name('agricultor.productos.store');
    Route::get('/agricultor/productos/{id}/editar', [AgricultorController::class, 'editarProducto'])->name('agricultor.productos.edit');
    Route::put('/agricultor/productos/{id}', [AgricultorController::class, 'actualizarProducto'])->name('agricultor.productos.update');
    Route::delete('/agricultor/productos/{id}', [AgricultorController::class, 'eliminarProducto'])->name('agricultor.productos.destroy');
    
    // ========== GESTIÓN DE PEDIDOS ==========
    // Pedidos pendientes (estado: pagado - esperando que el agricultor los prepare)
    Route::get('/agricultor/pedidos-pendientes', [AgricultorController::class, 'pedidosPendientes'])->name('agricultor.pedidos_pendientes');
    
    // Pedidos listos (estado: listo - preparados por el agricultor)
    Route::get('/agricultor/pedidos-listos', [AgricultorController::class, 'pedidosListos'])->name('agricultor.pedidos_listos');
    Route::get('/agricultor/pedidos_detalle', [AgricultorController::class, 'pedidosListos'])->name('agricultor.pedidos_detalle');

    // Ver detalle de un pedido específico
     Route::get('/agricultor/pedido/{id}', [AgricultorController::class, 'detallePedido'])->name('agricultor.pedido_detalle');
    
    // Marcar pedido como listo (agricultor confirma que preparó el pedido)
    Route::post('/agricultor/pedido/{id}/confirmar-listo', [AgricultorController::class, 'confirmarPedidoListo'])->name('agricultor.confirmar_pedido_listo');
    
    // ========== GESTIÓN DE PAGOS ==========
    // Ver pagos del agricultor (usando el sistema de semanas de feria)
    Route::get('/agricultor/pagos', [AgricultorController::class, 'pagos'])->name('agricultor.pagos');
    
    // Exportar reporte de pagos
    // Agregar esta ruta nueva
    Route::get('/agricultor/pagos/pdf', [AgricultorController::class, 'exportarPagosPDF'])->name('agricultor.pagos.pdf');
    
    // Ver detalle de pagos por semana
    Route::get('/agricultor/pagos/detalle', [AgricultorController::class, 'detallePagos'])->name('agricultor.detalle-pagos');
    Route::get('/admin/pagos/agricultor/{id}', [App\Http\Controllers\Admin\AdminController::class, 'detallePagoAgricultor'])
    ->name('admin.pagos.detalle-agricultor');
     // Ruta para ver pedidos expirados (admin)
    Route::get('/admin/pedidos/expirados', [App\Http\Controllers\Admin\AdminController::class, 'pedidosExpirados'])
        ->name('admin.pedidos.expirados');
    
    // Ruta para agricultores - ver sus pedidos expirados
    Route::get('/agricultor/pedidos/expirados', [App\Http\Controllers\OrderController::class, 'pedidosExpirados'])
        ->name('agricultor.pedidos.expirados');
       
    Route::get('/admin/pedidos/delivery', [App\Http\Controllers\Admin\AdminController::class, 'pedidosDelivery'])->name('admin.pedidos.delivery');
    Route::get('/admin/pedidos/recojo-puesto', [App\Http\Controllers\Admin\AdminController::class, 'pedidosRecojoPuesto'])->name('admin.pedidos.recojo-puesto');
});
});

//mercado pago
Route::post('/create-preference', [MercadoPagoController::class, 'createPaymentPreference']);
Route::get('/mercadopago/success', [MercadoPagoController::class, 'success'])->name('mercadopago.success');
Route::get('/mercadopago/failed', [MercadoPagoController::class, 'failed'])->name('mercadopago.failed');
// Route::get('/order/success/{orderId}', [OrderController::class, 'success'])->name('order.id');

//repartidor
Route::middleware(['auth'])->group(function () {
    // Dashboard del repartidor
    Route::get('/repartidor', [RepartidorController::class, 'index'])->name('repartidor.dashboard');
    
    // Gestión de entregas
    Route::get('/repartidor/pedidos-pendientes', [RepartidorController::class, 'pedidosPendientes'])
        ->name('repartidor.pedidos_pendientes');
    
    // ✅ RUTAS FALTANTES - AGREGAR ESTAS:
    Route::get('/repartidor/rutas', [RepartidorController::class, 'rutas'])
        ->name('repartidor.rutas');
    
    Route::get('/repartidor/entregas-completadas', [RepartidorController::class, 'entregasCompletadas'])
        ->name('repartidor.entregas_completadas');
    
    // Detalle y acciones de pedidos
    Route::get('/repartidor/pedido/{id}', [RepartidorController::class, 'detallePedido'])
        ->name('repartidor.pedido.detalle');
    
    Route::post('/repartidor/pedido/{id}/entregado', [RepartidorController::class, 'marcarComoEntregado'])
        ->name('repartidor.pedido.entregado');
    
    Route::post('/repartidor/pedido/{id}/proceso', [RepartidorController::class, 'marcarEnProceso'])
        ->name('repartidor.pedido.proceso');
});

//Rutas Admin - Agregar esto en web.php después de las rutas existentes

Route::middleware(['auth'])->group(function () {
    // Admin Dashboard y gestión
    Route::get('/admin', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('admin.dashboard');
    
    // Gestión de pedidos
    Route::get('/admin/pedidos', [App\Http\Controllers\Admin\AdminController::class, 'pedidos'])->name('admin.pedidos.index');
    Route::get('/admin/pedidos/pagados', [App\Http\Controllers\Admin\AdminController::class, 'pedidosPagados'])->name('admin.pedidos.pagados');
    Route::get('/admin/pedidos/listos', [App\Http\Controllers\Admin\AdminController::class, 'pedidosListos'])->name('admin.pedidos.listos');
    Route::get('/admin/pedidos/armados', [App\Http\Controllers\Admin\AdminController::class, 'pedidosArmados'])->name('admin.pedidos.armados');
    Route::get('/admin/pedido/{id}', [App\Http\Controllers\Admin\AdminController::class, 'detallePedido'])->name('admin.pedido.detalle');
    Route::post('/admin/pedido/{id}/estado', [App\Http\Controllers\Admin\AdminController::class, 'cambiarEstado'])->name('admin.pedido.estado');
    
    // ==================== PAGOS A AGRICULTORES - CORREGIDAS ====================
    Route::get('/admin/pagos/agricultores', [App\Http\Controllers\Admin\AdminController::class, 'pagosAgricultores'])->name('admin.pagos.agricultores');
    Route::get('/admin/pagos/agricultor/{agricultor}', [App\Http\Controllers\Admin\AdminController::class, 'detallePagoAgricultor'])->name('admin.pagos.detalle-agricultor');
    Route::get('/admin/pagos/exportar', [App\Http\Controllers\Admin\AdminController::class, 'exportarPagos'])->name('admin.pagos.exportar');
    
    // ==================== REPORTES ====================
    Route::get('/admin/reportes/semanales', [App\Http\Controllers\Admin\AdminController::class, 'reportesSemanales'])->name('admin.reportes.semanales');
    
    // Configuraciones del sistema
    Route::get('/admin/configuracion/zonas', [App\Http\Controllers\Admin\AdminController::class, 'zonas'])->name('admin.configuracion.zonas');
    Route::get('/admin/configuracion/zonas/crear', [App\Http\Controllers\Admin\AdminController::class, 'crearZona'])->name('admin.configuracion.zonas.crear');
    Route::post('/admin/configuracion/zonas', [App\Http\Controllers\Admin\AdminController::class, 'guardarZona'])->name('admin.configuracion.zonas.guardar');
    Route::get('/admin/configuracion/zonas/{id}/editar', [App\Http\Controllers\Admin\AdminController::class, 'editarZona'])->name('admin.configuracion.zonas.editar');
    Route::put('/admin/configuracion/zonas/{id}', [App\Http\Controllers\Admin\AdminController::class, 'actualizarZona'])->name('admin.configuracion.zonas.actualizar');
    Route::delete('/admin/configuracion/zonas/{id}', [App\Http\Controllers\Admin\AdminController::class, 'eliminarZona'])->name('admin.configuracion.zonas.eliminar');
    
    Route::get('/admin/configuracion/categorias', [App\Http\Controllers\Admin\AdminController::class, 'categorias'])->name('admin.configuracion.categorias');
    Route::get('/admin/configuracion/categorias/crear', [App\Http\Controllers\Admin\AdminController::class, 'crearCategoria'])->name('admin.configuracion.categorias.crear');
    Route::post('/admin/configuracion/categorias', [App\Http\Controllers\Admin\AdminController::class, 'guardarCategoria'])->name('admin.configuracion.categorias.guardar');
    Route::get('/admin/configuracion/categorias/{id}/editar', [App\Http\Controllers\Admin\AdminController::class, 'editarCategoria'])->name('admin.configuracion.categorias.editar');
    Route::put('/admin/configuracion/categorias/{id}', [App\Http\Controllers\Admin\AdminController::class, 'actualizarCategoria'])->name('admin.configuracion.categorias.actualizar');
    Route::delete('/admin/configuracion/categorias/{id}', [App\Http\Controllers\Admin\AdminController::class, 'eliminarCategoria'])->name('admin.configuracion.categorias.eliminar');
        
    Route::get('/admin/configuracion/medidas', [App\Http\Controllers\Admin\AdminController::class, 'medidas'])->name('admin.configuracion.medidas');
    Route::get('/admin/configuracion/medidas/crear', [App\Http\Controllers\Admin\AdminController::class, 'crearMedida'])->name('admin.configuracion.medidas.crear');
    Route::post('/admin/configuracion/medidas', [App\Http\Controllers\Admin\AdminController::class, 'guardarMedida'])->name('admin.configuracion.medidas.guardar');
    Route::get('/admin/configuracion/medidas/{id}/editar', [App\Http\Controllers\Admin\AdminController::class, 'editarMedida'])->name('admin.configuracion.medidas.editar');
    Route::put('/admin/configuracion/medidas/{id}', [App\Http\Controllers\Admin\AdminController::class, 'actualizarMedida'])->name('admin.configuracion.medidas.actualizar');
    Route::delete('/admin/configuracion/medidas/{id}', [App\Http\Controllers\Admin\AdminController::class, 'eliminarMedida'])->name('admin.configuracion.medidas.eliminar');
    
    Route::get('/admin/configuracion/mercados', [App\Http\Controllers\Admin\AdminController::class, 'mercados'])->name('admin.configuracion.mercados');

    // Ruta para migrar pedidos existentes al repartidor del sistema (solo para development/testing)
    Route::get('/admin/migrar-pedidos-sistema', function() {
        if (app()->environment('local')) {
            $repartidorSistema = \App\Models\User::where('email', 'sistema.repartidor@puntoVerde.com')->first();
            
            if ($repartidorSistema) {
                \App\Models\Order::whereNull('repartidor_id')
                                ->orWhere('repartidor_id', 0)
                                ->update(['repartidor_id' => $repartidorSistema->id]);
                
                return response()->json(['message' => 'Pedidos migrados al repartidor del sistema']);
            }
            
            return response()->json(['error' => 'Repartidor del sistema no encontrado']);
        }
        
        abort(404);
    })->name('admin.migrar.pedidos');

     // Gestión de repartidores (enlace desde admin dashboard)
    Route::get('/admin/repartidores', [App\Http\Controllers\Admin\AdminController::class, 'repartidores'])
        ->name('admin.repartidores');
    
    // Resumen de entregas del día
    Route::get('/admin/entregas/resumen', [App\Http\Controllers\Admin\AdminController::class, 'resumenEntregas'])
        ->name('admin.entregas.resumen');
    
    // Transferir pedido manualmente
    Route::post('/admin/pedido/transferir', [App\Http\Controllers\Admin\AdminController::class, 'transferirPedido'])
        ->name('admin.pedido.transferir');
    
    // Vista de pedidos agrupados por zona
    Route::get('/admin/pedidos/por-zona', [App\Http\Controllers\Admin\AdminController::class, 'pedidosPorZona'])
        ->name('admin.pedidos.por-zona');

    // ==================== CREAR AdminRepartidorController ====================
    Route::prefix('admin/repartidores')->name('admin.repartidores.')->group(function () {
        
        // Lista principal de repartidores
        Route::get('/', [App\Http\Controllers\Admin\AdminRepartidorController::class, 'index'])
            ->name('index');
        
        // Asignar zonas a repartidor
        Route::post('/asignar', [App\Http\Controllers\Admin\AdminRepartidorController::class, 'asignarZonas'])
            ->name('asignar');
        
        // Quitar asignación de zona
        Route::post('/quitar', [App\Http\Controllers\Admin\AdminRepartidorController::class, 'quitarAsignacion'])
            ->name('quitar');
        
        // Ver detalle de repartidor específico
        Route::get('/{repartidor}/detalle', [App\Http\Controllers\Admin\AdminRepartidorController::class, 'detalle'])
            ->name('detalle');
    });
});

// En web.php - SOLO PARA TESTING
Route::get('/test-voucher/{orderId}', function($orderId) {
    $orden = \App\Models\Order::with(['items.product', 'user'])->findOrFail($orderId);
    
    $subtotal = $orden->items->sum(function($item) {
        return $item->precio * $item->cantidad;
    });

    $costoEnvio = 0;
    if ($orden->delivery === 'delivery' && $orden->distrito) {
        $zona = \App\Models\Zone::where('name', $orden->distrito)->first();
        if ($zona) {
            $costoEnvio = $zona->delivery_cost;
        }
    }

    $total = $subtotal + $costoEnvio;
    
    // Mostrar la vista sin PDF para verificar que funciona
    return view('order.voucher', compact('orden', 'subtotal', 'costoEnvio', 'total'));
})->middleware('auth');