<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher - Orden #{{ $orden->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            background: #fff;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #28a745;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #28a745;
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 16px;
        }
        
        .order-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        
        .order-info .left, .order-info .right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
        
        .info-section h3 {
            color: #28a745;
            font-size: 16px;
            margin-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 5px;
        }
        
        .info-section p {
            margin: 8px 0;
            font-size: 14px;
        }
        
        .info-section .label {
            font-weight: bold;
            color: #495057;
            display: inline-block;
            width: 40%;
        }
        
        .info-section .value {
            color: #333;
            display: inline-block;
            width: 60%;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pagado {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-pendiente {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .delivery-info {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .delivery-info h3 {
            color: #28a745;
            font-size: 16px;
            margin-bottom: 15px;
        }
        
        .delivery-info p {
            margin: 8px 0;
            font-size: 14px;
        }
        
        .delivery-info .label {
            font-weight: bold;
            color: #495057;
            display: inline-block;
            width: 30%;
        }
        
        .delivery-info .value {
            color: #333;
            display: inline-block;
            width: 70%;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            border: 1px solid #dee2e6;
        }
        
        .items-table th {
            background: #28a745;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 14px;
        }
        
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e9ecef;
            background: white;
            font-size: 13px;
        }
        
        .items-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .product-name {
            font-weight: bold;
            color: #333;
        }
        
        .quantity {
            text-align: center;
            font-weight: bold;
            color: #28a745;
        }
        
        .price {
            text-align: right;
            font-weight: bold;
        }
        
        .totals {
            margin-top: 30px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 5px solid #28a745;
        }
        
        .total-row {
            display: table;
            width: 100%;
            margin: 8px 0;
            padding: 6px 0;
            font-size: 16px;
        }
        
        .total-row .label {
            display: table-cell;
            text-align: left;
        }
        
        .total-row .amount {
            display: table-cell;
            text-align: right;
            font-weight: bold;
        }
        
        .total-row.subtotal {
            border-bottom: 1px solid #dee2e6;
            color: #6c757d;
        }
        
        .total-row.shipping {
            border-bottom: 1px solid #dee2e6;
            color: #6c757d;
        }
        
        .total-row.final {
            border-top: 2px solid #28a745;
            font-weight: bold;
            font-size: 18px;
            color: #28a745;
            margin-top: 15px;
            padding-top: 15px;
        }
        
        .thank-you {
            background: #28a745;
            color: white;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
            text-align: center;
        }
        
        .thank-you h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .thank-you p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
        }
        
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>VOUCHER DE COMPRA</h1>
            <div class="subtitle">Punto Verde - Productos Frescos y Naturales</div>
        </div>

        <!-- Order Information -->
        <div class="order-info">
            <div class="left">
                <div class="info-section">
                    <h3>Información de la Orden</h3>
                    <p>
                        <span class="label">Número de Orden:</span>
                        <span class="value">#{{ str_pad($orden->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </p>
                    <p>
                        <span class="label">Fecha de Compra:</span>
                        <span class="value">{{ $orden->created_at->format('d/m/Y H:i') }}</span>
                    </p>
                    <p>
                        <span class="label">Estado:</span>
                        <span class="value">
                            <span class="status-badge {{ $orden->estado === 'pagado' ? 'status-pagado' : 'status-pendiente' }}">
                                {{ $orden->estado === 'pagado' ? 'Pagado' : 'Pendiente' }}
                            </span>
                        </span>
                    </p>
                    @if($orden->paid_at)
                    <p>
                        <span class="label">Fecha de Pago:</span>
                        <span class="value">{{ $orden->paid_at->format('d/m/Y H:i') }}</span>
                    </p>
                    @endif
                </div>
            </div>

            <div class="right">
                <div class="info-section">
                    <h3>Datos del Cliente</h3>
                    <p>
                        <span class="label">Nombre:</span>
                        <span class="value">{{ $orden->nombre }} {{ $orden->apellido }}</span>
                    </p>
                    <p>
                        <span class="label">Email:</span>
                        <span class="value">{{ $orden->email }}</span>
                    </p>
                    <p>
                        <span class="label">Teléfono:</span>
                        <span class="value">{{ $orden->telefono }}</span>
                    </p>
                    @if($orden->empresa)
                    <p>
                        <span class="label">Empresa:</span>
                        <span class="value">{{ $orden->empresa }}</span>
                    </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Delivery Information -->
        <div class="delivery-info">
            <h3>Información de Entrega</h3>
            <p>
                <span class="label">Tipo de Entrega:</span>
                <span class="value">
                    @if($orden->delivery === 'puesto')
                        Recoger en Puesto
                    @else
                        Delivery a Domicilio
                    @endif
                </span>
            </p>
            @if($orden->delivery === 'delivery')
                <p>
                    <span class="label">Dirección:</span>
                    <span class="value">{{ $orden->direccion }}</span>
                </p>
                <p>
                    <span class="label">Distrito:</span>
                    <span class="value">{{ $orden->distrito }}</span>
                </p>
            @endif
        </div>

        <!-- Products Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Producto</th>
                    <th style="width: 15%; text-align: center;">Cantidad</th>
                    <th style="width: 17.5%; text-align: right;">Precio Unit.</th>
                    <th style="width: 17.5%; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orden->items as $item)
                <tr>
                    <td class="product-name">{{ $item->product->nombre }}</td>
                    <td class="quantity">{{ $item->cantidad }}</td>
                    <td class="price">S/{{ number_format($item->precio, 2) }}</td>
                    <td class="price">S/{{ number_format($item->precio * $item->cantidad, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <!-- Totals - Reemplaza esta sección en tu voucher -->
        <div class="totals">
            <div class="total-row subtotal">
                <span class="label">Subtotal de Productos:</span>
                <span class="amount">S/{{ number_format($subtotal, 2) }}</span>
            </div>
            
            @if($costoEnvio > 0)
            <div class="total-row shipping">
                <span class="label">Costo de Envío:</span>
                <span class="amount">S/{{ number_format($costoEnvio, 2) }}</span>
            </div>
            @endif

            <div class="total-row subtotal">
                <span class="label">Subtotal:</span>
                <span class="amount">S/{{ number_format($montoNeto, 2) }}</span>
            </div>
            
            <!-- NUEVA SECCIÓN: Comisión MercadoPago -->
            <div class="total-row shipping">
                <span class="label">Comisión Pago Seguro:</span>
                <span class="amount">S/{{ number_format($comisionCobrada, 2) }}</span>
            </div>
            
            <div class="total-row final">
                <span class="label">TOTAL PAGADO:</span>
                <span class="amount">S/{{ number_format($totalConComision, 2) }}</span>
            </div>
        </div>

        <!-- Agregar información adicional sobre la comisión -->
        <div style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; font-size: 13px; color: #1565c0;">
                <strong>Información sobre la comisión:</strong> 
                La comisión de pago seguro garantiza transacciones protegidas a través de MercadoPago 
                y permite que los agricultores reciban el precio justo por sus productos.
            </p>
        </div>

        <!-- Thank You Message -->
        <div class="thank-you">
            <h3>¡Gracias por tu compra!</h3>
            <p>Esperamos que disfrutes de nuestros productos frescos y naturales.</p>
            <p>Tu pedido será procesado y preparado con el mayor cuidado.</p>
            <p style="font-size: 13px; margin-top: 10px;">
                Tu pago fue procesado de forma 100% segura a través de MercadoPago
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Punto Verde - Productos Frescos y Naturales</strong></p>
            <p>Contacto:  islasdepazperu.org</p>
            <p>whttps://islasdepazperu.org/</p>
            <p style="margin-top: 15px;">
                Este voucher fue generado automáticamente el {{ now()->format('d/m/Y H:i:s') }}
            </p>
        </div>
    </div>
</body>
</html>