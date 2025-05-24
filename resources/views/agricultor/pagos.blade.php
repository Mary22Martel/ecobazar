@extends('layouts.app2')

@section('content')
<div class="container mx-auto my-10 px-4">
  <h1 class="text-4xl font-bold text-center text-green-600 mb-8">Pagos al Productor</h1>

  @if($pagos->isEmpty())
    <p class="text-center text-gray-700">No hay ventas pendientes de pago.</p>
  @else
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white shadow rounded-lg">
        <thead>
          <tr class="bg-gray-100 text-gray-800">
            <th class="px-6 py-3 text-left">Producto</th>
            <th class="px-6 py-3 text-right">Cantidad Vendida</th>
            <th class="px-6 py-3 text-right">Monto a Pagar (S/)</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @foreach($pagos as $pago)
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4">{{ $pago['producto']->nombre }}</td>
              <td class="px-6 py-4 text-right">{{ $pago['cantidad'] }}</td>
              <td class="px-6 py-4 text-right">{{ number_format($pago['monto'], 2) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr class="bg-gray-100 font-bold">
            <td class="px-6 py-4">Total a pagar</td>
            <td class="px-6 py-4"></td>
            <td class="px-6 py-4 text-right">{{ number_format($totalPagar, 2) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>
  @endif
</div>
@endsection
