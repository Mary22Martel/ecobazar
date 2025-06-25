<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Pagos - {{ $agricultor->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .info { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .total { background-color: #e8f5e8; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2> REPORTE DE PAGOS</h2>
        <h3>{{ $agricultor->name }}</h3>
        <p>Período: {{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }}</p>
        <p>Entrega: {{ $diaEntrega->format('l, d/m/Y') }}</p>
    </div>

    <div class="info">
        <p><strong>Total a Recibir:</strong> S/ {{ number_format($totalPagar, 2) }}</p>
        <p><strong>Productos Vendidos:</strong> {{ $totalProductos }}</p>
        <p><strong>Cantidad Total:</strong> {{ number_format($totalCantidad) }}</p>
        <p><strong>Pedidos Armados:</strong> {{ $totalPedidos }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-right">Cantidad</th>
                <th class="text-right">Precio Prom.</th>
                <th class="text-right">Pedidos</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagos as $pago)
            <tr>
                <td>{{ $pago['producto']->nombre }}</td>
                <td class="text-right">{{ number_format($pago['cantidad']) }}</td>
                <td class="text-right">S/ {{ number_format($pago['precio_promedio'], 2) }}</td>
                <td class="text-right">{{ $pago['pedidos_count'] }}</td>
                <td class="text-right">S/ {{ number_format($pago['monto'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="4" class="text-right"><strong>TOTAL A PAGAR:</strong></td>
                <td class="text-right"><strong>S/ {{ number_format($totalPagar, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; font-size: 10px; color: #666;">
        <p> Generado el {{ now()->format('d/m/Y H:i') }}</p>
        <p> Solo se incluyen pedidos en estado ARMADO</p>
    </div>
</body>
</html>