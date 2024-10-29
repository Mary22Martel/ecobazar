<!-- resources/views/order/voucher.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher de Orden</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 2rem;
        }
        .details {
            margin-bottom: 20px;
        }
        .details p {
            margin: 5px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f8f8f8;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            font-size: 1.2rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Voucher de Orden</h1>
            <p>Orden ID: {{ $orden->id }}</p>
            <p>Fecha: {{ $orden->created_at->format('d/m/Y') }}</p>
        </div>

        <div class="details">
            <h3>Detalles del Cliente</h3>
            <p><strong>Nombre:</strong> {{ $orden->nombre }} {{ $orden->apellido }}</p>
            <p><strong>Correo Electrónico:</strong> {{ $orden->email }}</p>
            <p><strong>Teléfono:</strong> {{ $orden->telefono }}</p>
        </div>

        <div class="details">
            <h3>Opciones de Delivery</h3>
            <p><strong>Tipo de Delivery:</strong> {{ $orden->delivery == 'puesto' ? 'Recoger en Puesto' : 'Delivery' }}</p>
            @if($orden->delivery == 'delivery')
                <p><strong>Dirección:</strong> {{ $orden->direccion }}, {{ $orden->distrito }}</p>
            @endif
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orden->items as $item)
                <tr>
                    <td>{{ $item->product->nombre }}</td>
                    <td>{{ $item->cantidad }}</td>
                    <td>S/{{ number_format($item->precio, 2) }}</td>
                    <td>S/{{ number_format($item->precio * $item->cantidad, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            <p>Subtotal: S/{{ number_format($orden->subtotal, 2) }}</p>
            <p>Envió: S/{{ number_format($orden->envio, 2) }}</p>
            <p>Total: S/{{ number_format($orden->total, 2) }}</p>
        </div>

    </div>
</body>
</html>

