<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Carrito;
use App\Models\Zone;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Exception;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            Log::info('Entrando en el método store del controlador OrderController');
    
            // Si la entrega no es "delivery", elimina el campo distrito de la solicitud
            if ($request->input('delivery') !== 'delivery') {
                $request->merge(['distrito' => null]);
            }
    
            // Validación de los datos de entrada
            $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'empresa' => 'nullable|string|max:255',
                'email' => 'required|email',
                'telefono' => 'required|string|max:20',
                'delivery' => 'required|string',
                'direccion' => 'nullable|string|max:255|required_if:delivery,delivery',
                'distrito' => 'nullable|required_if:delivery,delivery|exists:zones,id',
                'pago' => 'required|string',
            ]);
    
            Log::info('Validación completada con éxito:', $request->all());
    
            // Obtener carrito y verificar si está vacío
            $carrito = Carrito::where('user_id', Auth::id())->with('items.product')->first();
            if (!$carrito || $carrito->items->isEmpty()) {
                Log::warning('Carrito vacío o no encontrado para el usuario ID: ' . Auth::id());
                return response()->json(['error' => 'El carrito está vacío.'], 400);
            }
    
            // Calcular subtotal
            $subtotal = $carrito->items->sum(fn($item) => $item->product->precio * $item->cantidad);
            $envio = 0.00;
            $zone = null;
            $repartidor = null;
    
            // Calcular costo de envío y asignar repartidor si es delivery
            if ($request->input('delivery') === 'delivery') {
                $zone = Zone::find($request->input('distrito'));
                if (!$zone) {
                    return response()->json(['error' => 'La zona seleccionada no es válida.'], 400);
                }
                $envio = (float) $zone->delivery_cost;
    
                // Buscar un repartidor que esté asignado a esta zona
                $repartidor = User::whereHas('zones', function ($query) use ($zone) {
                    $query->where('zones.id', $zone->id);
                })->first();
    
                if (!$repartidor) {
                    Log::warning("No hay repartidores disponibles para la zona ID: {$zone->id}");
                    return response()->json(['error' => 'No hay repartidores disponibles para esta zona.'], 400);
                }
    
                Log::info("Repartidor asignado con ID: {$repartidor->id} para la zona ID: {$zone->id}");
            }
    
            $estado = $request->input('pago') === 'sistema' ? 'pendiente de pago' : 'pendiente en puesto';
    
            // Crear el pedido
            $order = Order::create([
                'user_id' => Auth::id(),
                'nombre' => $request->input('nombre'),
                'apellido' => $request->input('apellido'),
                'empresa' => $request->input('empresa'),
                'email' => $request->input('email'),
                'telefono' => $request->input('telefono'),
                'delivery' => $request->input('delivery'),
                'direccion' => $request->input('direccion'),
                'distrito' => $zone->name ?? null,
                'pago' => $request->input('pago'),
                'total' => $subtotal + $envio,
                'estado' => $estado,
                'repartidor_id' => $repartidor ? $repartidor->id : null, // Asignación del repartidor
            ]);
    
            Log::info("Pedido creado exitosamente con ID: {$order->id}" . ($repartidor ? " y asignado al repartidor ID: {$repartidor->id}" : ""));

    
            // Preparar items para Mercado Pago
            $itemsParaMercadoPago = [];
            foreach ($carrito->items as $item) {
                $order->items()->create([
                    'producto_id' => $item->producto_id,
                    'cantidad' => $item->cantidad,
                    'precio' => $item->product->precio,
                ]);
    
                // Item para Mercado Pago
                $itemsParaMercadoPago[] = [
                    "title" => $item->product->nombre,
                    "quantity" => $item->cantidad,
                    "currency_id" => "PEN",
                    "unit_price" => floatval($item->product->precio),
                ];
    
                // Actualizar inventario
                $producto = $item->product;
                if ($producto->cantidad_disponible < $item->cantidad) {
                    return redirect()->route('carrito.index')->with('error', 'No hay suficiente stock para el producto: ' . $producto->nombre);
                }
                $producto->cantidad_disponible -= $item->cantidad;
                $producto->save();
            }
    
            $carrito->items()->delete();
    
            // Si es pago en sistema, crear la preferencia de Mercado Pago
            if ($request->input('pago') === 'sistema') {
                try {
                    Log::info('Iniciando autenticación de Mercado Pago');
                    MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
                    $client = new PreferenceClient();
                    Log::info('Cliente de preferencia creado correctamente');
            
                    $requestData = [
                        "items" => $itemsParaMercadoPago,
                        "back_urls" => [
                            "success" => route('order.success', ['orderId' => $order->id]),
                            "failure" => route('order.failed'),
                        ],
                        "auto_return" => "approved",
                        "shipments" => [
                            "cost" => $envio, // Asegúrate de que sea un float
                            "mode" => "not_specified"
                        ]
                    ];
            
                    // Registrar requestData como JSON para evitar errores de tipo Array to string conversion
                    Log::info('Datos de la preferencia de Mercado Pago: ' . json_encode($requestData));
            
                    $preference = $client->create($requestData);
                    Log::info('Preferencia creada con éxito, URL: ' . $preference->init_point);
            
                    return response()->json(['init_point' => $preference->init_point]);
                } catch (MPApiException $error) {
                    $errorMessage = 'Error de Mercado Pago API: ' . $error->getApiResponse()->getContent();
                    Log::error($errorMessage);
                    return response()->json(['error' => 'Error al procesar el pago: ' . $errorMessage], 500);
                } catch (Exception $e) {
                    $errorMessage = 'Error de Mercado Pago General: ' . $e->getMessage();
                    Log::error($errorMessage);
                    return response()->json(['error' => 'Error al procesar el pago: ' . $errorMessage], 500);
                }
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Pedido creado exitosamente.',
                'redirect_url' => route('order.success', ['orderId' => $order->id]) 
            ]);
        } catch (\Exception $e) {
            Log::error('Error al crear la orden: ' . $e->getMessage());
            return response()->json(['error' => 'Hubo un problema al crear la orden: ' . $e->getMessage()], 500);
        }
    }
    


    
    

public function success($orderId)
{
    $orden = Order::with('items.product')->findOrFail($orderId);

    // Cambiar el estado a "pagado" si aún está en "pendiente de pago"
    if ($orden->estado === 'pendiente de pago') {
        $orden->estado = 'pagado';
        $orden->save();
    }

    // Calcular subtotal y envío
    $subtotal = $orden->items->sum(function ($item) {
        return $item->precio * $item->cantidad;
    });
    
    // Obtener el costo de envío en base a la zona si es "delivery"
    $envio = $orden->delivery === 'delivery' ? Zone::where('name', $orden->distrito)->value('delivery_cost') : 0.00;

    if (!$orden->subtotal || !$orden->envio) {
        $orden->subtotal = $subtotal;
        $orden->envio = $envio;
        $orden->total = $subtotal + $envio;
        $orden->save();
    }

    return view('order.success', compact('orden'));
}

    

    
    
public function downloadVoucher($orderId)
{
    $orden = Order::with('items.product')->findOrFail($orderId);

    $subtotal = $orden->items->sum(function($item) {
        return $item->precio * $item->cantidad;
    });

    // Obtener el costo de envío en base a la zona si es "delivery"
    $envio = $orden->delivery === 'delivery' ? Zone::where('name', $orden->distrito)->value('delivery_cost') : 0.00;
    $total = $subtotal + $envio;

    // Pasar los valores a la vista del PDF
    $pdf = PDF::loadView('order.voucher', [
        'orden' => $orden,
        'subtotal' => $subtotal,
        'envio' => $envio,
        'total' => $total,
    ]);

    return $pdf->download('voucher_orden_' . $orderId . '.pdf');
}

    
    
public function voucher($orderId)
{
    $orden = Order::with('items.product')->findOrFail($orderId);

    $subtotal = $orden->items->sum(function ($item) {
        return $item->precio * $item->cantidad;
    });

    // Obtener el costo de envío en base a la zona si es "delivery"
    $envio = $orden->delivery === 'delivery' ? Zone::where('name', $orden->distrito)->value('delivery_cost') : 0.00;

    if (!$orden->subtotal || !$orden->envio) {
        $orden->subtotal = $subtotal;
        $orden->envio = $envio;
        $orden->total = $subtotal + $envio;
        $orden->save();
    }

    $pdf = PDF::loadView('order.voucher', compact('orden'));
    return $pdf->download('voucher_orden_' . $orderId . '.pdf');
}



    public function pedidosPendientes()
    {
        // Verificar si el usuario es agricultor
        if (Auth::user()->role !== 'agricultor') {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        // Obtener los pedidos relacionados a los productos de este agricultor
        $pedidos = Order::whereHas('items.product', function ($query) {
            $query->where('user_id', Auth::id());
        })->with('items.product')->get();

        return view('agricultor.pedidos_pendientes', compact('pedidos'));
    }

    public function detallePedido($id)
    {
        // Verificar si el usuario es agricultor
        if (Auth::user()->role !== 'agricultor') {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        $pedido = Order::with('items.product')->findOrFail($id);
        
        // Verificar si el agricultor tiene productos en este pedido
        $productosAgricultor = $pedido->items->filter(function ($item) {
            return $item->product->user_id == Auth::id();
        });

        if ($productosAgricultor->isEmpty()) {
            abort(403, 'No tienes autorización para ver este pedido.');
        }

        return view('agricultor.pedido_detalle', compact('pedido', 'productosAgricultor'));
    }

    public function mostrarPedidosPendientes()
    {
        // Verificar si el usuario es agricultor
        if (Auth::user()->role !== 'agricultor') {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        $pedidos = Order::whereHas('items.product', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();

        return view('agricultor.pedidos_pendientes', compact('pedidos'));
    }

    public function confirmarPedidoListo($id)
    {
        $pedido = Order::findOrFail($id);

        // Verificar si el agricultor tiene productos en este pedido
        $productosAgricultor = $pedido->items->filter(function ($item) {
            return $item->product->user_id == Auth::id();
        });

        if ($productosAgricultor->isEmpty()) {
            abort(403, 'No tienes autorización para modificar este pedido.');
        }

        // Cambiar el estado del pedido a 'listo'
        $pedido->estado = 'listo';
        $pedido->save();

        return redirect()->route('agricultor.pedidos_pendientes')->with('success', 'Pedido confirmado como listo.');
    }

    public function pedidosListos()
    {
        $pedidos = Order::whereHas('items.product', function ($query) {
            $query->where('user_id', Auth::id());
        })->where('estado', 'listo')->get();

        return view('agricultor.pedidos_listos', compact('pedidos'));
    }

    public function todosLosPedidos()
    {
        $pedidos = Order::with(['items.product', 'user'])->get();
        return view('admin.pedidos.index', compact('pedidos'));
    }

    public function detallePedidoAdmin($id)
    {
        $pedido = Order::with(['items.product.usuario', 'user'])->findOrFail($id);
        return view('admin.pedidos.detalle', compact('pedido'));
    }

    public function actualizarEstado(Request $request, $id)
    {
        $pedido = Order::findOrFail($id);
        $pedido->estado = $request->input('estado');
        $pedido->save();

        return redirect()->route('admin.pedidos.index')->with('success', 'Estado del pedido actualizado correctamente.');
    }

    public function failed()
    {
        return view('order.failed'); // Asegúrate de tener la vista 'order.failed' en tu carpeta de vistas
    }

}
