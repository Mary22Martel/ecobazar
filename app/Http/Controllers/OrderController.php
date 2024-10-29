<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Carrito;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Exception;



class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Validar los datos de la solicitud
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'empresa' => 'nullable|string|max:255',
            'email' => 'required|email',
            'telefono' => 'required|string|max:20',
            'delivery' => 'required|string',
            'direccion' => 'required_if:delivery,delivery|string|max:255',
            'distrito' => 'required_if:delivery,delivery|string|max:255',
            'pago' => 'required|string',
        ]);

        // Obtener el carrito del usuario
        $carrito = Carrito::where('user_id', Auth::id())->with('items.product')->first();

        if (!$carrito || $carrito->items->isEmpty()) {
            return redirect()->route('carrito.index')->with('error', 'El carrito está vacío.');
        }

        $order = Order::create([
            'user_id' => Auth::id(),
            'nombre' => $request->input('nombre'),
            'apellido' => $request->input('apellido'),
            'empresa' => $request->input('empresa'),
            'email' => $request->input('email'),
            'telefono' => $request->input('telefono'),
            'delivery' => $request->input('delivery'),
            'direccion' => $request->input('direccion'),
            'distrito' => $request->input('distrito'),
            'pago' => $request->input('pago'),
            'total' => $carrito->total() + 8.00, // Asumiendo un costo de envío fijo
            'estado' => $request->input('pago') === 'sistema' ? 'pendiente de pago' : 'en proceso',
        ]);
        

        // Lógica para Mercado Pago
        if ($request->input('pago') === 'sistema') {
            try {
                // Autenticar con Mercado Pago
                MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
                MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

                // Crear la preferencia de pago
                $client = new PreferenceClient();

                $items = [];
                foreach ($carrito->items as $item) {
                    $items[] = [
                        "title" => $item->product->nombre,
                        "quantity" => $item->cantidad,
                        "currency_id" => "PEN",
                        "unit_price" => $item->product->precio,
                    ];
                }

                $requestData = [
                    "items" => $items,
                    "back_urls" => [
                        "success" => route('order.success', ['orderId' => $order->id]),
                        "failure" => route('order.failed'),
                    ],
                    "auto_return" => "approved"
                ];

                $preference = $client->create($requestData);

                // Redirigir al usuario a la URL de pago
                return redirect($preference->init_point);

            } catch (MPApiException $error) {
                return redirect()->route('carrito.checkout')->with('error', 'Error al procesar el pago: ' . $error->getApiResponse()->getContent());
            } catch (Exception $e) {
                return redirect()->route('carrito.checkout')->with('error', 'Error al procesar el pago: ' . $e->getMessage());
            }
        }

        // Lógica para pagar en el puesto
        // Asociar los productos del carrito con la orden y reducir el stock
        foreach ($carrito->items as $item) {
            $order->items()->create([
                'producto_id' => $item->producto_id,
                'cantidad' => $item->cantidad,
                'precio' => $item->product->precio,
            ]);

            // Reducir el stock del producto
            $producto = $item->product;
            if ($producto->cantidad_disponible < $item->cantidad) {
                return redirect()->route('carrito.index')->with('error', 'No hay suficiente stock para el producto: ' . $producto->nombre);
            }
            $producto->cantidad_disponible -= $item->cantidad;
            $producto->save();
        }

        // Vaciar el carrito después de realizar la orden
        $carrito->items()->delete();

        return redirect()->route('order.success', ['orderId' => $order->id])->with('success', 'Tu pedido ha sido realizado con éxito.');
    }

    

    public function success($orderId)
{
    $orden = Order::with('items.product')->findOrFail($orderId);

    // Verificar si la orden ya fue procesada para evitar duplicar el proceso
    if ($orden->estado == 'pagado') {
        return view('order.success', compact('orden'));
    }

    // Actualizar el estado del pedido a pagado
    $orden->estado = 'pagado';
    $orden->save();

    // Asociar los productos del carrito con la orden y reducir el stock
    $carrito = Carrito::where('user_id', Auth::id())->with('items.product')->first();
    if ($carrito) {
        foreach ($carrito->items as $item) {
            $orden->items()->create([
                'producto_id' => $item->producto_id,
                'cantidad' => $item->cantidad,
                'precio' => $item->product->precio,
            ]);

            // Reducir el stock del producto
            $producto = $item->product;
            if ($producto->cantidad_disponible < $item->cantidad) {
                return redirect()->route('carrito.index')->with('error', 'No hay suficiente stock para el producto: ' . $producto->nombre);
            }
            $producto->cantidad_disponible -= $item->cantidad;
            $producto->save();
        }

        // Vaciar el carrito después de realizar la orden
        $carrito->items()->delete();
    }

    // Calcular el subtotal sumando todos los productos de la orden
    $subtotal = $orden->items->sum(function ($item) {
        return $item->precio * $item->cantidad;
    });

    // Asignar el costo de envío dependiendo del tipo de entrega
    $envio = $orden->delivery == 'delivery' ? 8.00 : 0.00;

    // Asignar los valores calculados a la orden
    $orden->subtotal = $subtotal;
    $orden->envio = $envio;

    return view('order.success', compact('orden'));
}

    

    public function downloadVoucher($orderId)
    {
        $orden = Order::with('items.product')->findOrFail($orderId);
    
        // Calcular el subtotal sumando todos los productos de la orden
        $subtotal = $orden->items->sum(function($item) {
            return $item->precio * $item->cantidad;
        });
    
        // Asignar el costo de envío dependiendo del tipo de entrega
        $envio = $orden->delivery == 'delivery' ? 8.00 : 0.00;
    
        // Asignar los valores calculados a la orden
        $orden->subtotal = $subtotal;
        $orden->envio = $envio;
    
        // Calcular el total
        $orden->total = $subtotal + $envio;
    
        $pdf = PDF::loadView('order.voucher', compact('orden'));
        return $pdf->download('voucher_orden_' . $orderId . '.pdf');
    }
    

public function voucher($orderId)
{
    $orden = Order::with('items.product')->findOrFail($orderId);

    // Calcular el subtotal sumando todos los productos de la orden
    $subtotal = $orden->items->sum(function($item) {
        return $item->precio * $item->cantidad;
    });

    // Asignar el costo de envío dependiendo del tipo de entrega
    $envio = $orden->delivery == 'delivery' ? 8.00 : 0.00;

    // Asignar los valores calculados a la orden (necesitas hacerlo manualmente para que estén disponibles en la vista)
    $orden->subtotal = $subtotal;
    $orden->envio = $envio;

    // Calcular el total
    $orden->total = $subtotal + $envio;

    // Retornar la vista del voucher
    return view('order.voucher', compact('orden'));
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

}
