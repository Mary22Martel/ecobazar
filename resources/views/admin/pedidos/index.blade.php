@extends('layouts.app2')

@section('content')
<div class="space-y-8">
    <!-- Tabla de Pedidos Pagados -->
    <div>
        <h3 class="text-2xl font-semibold text-green-600">Pedidos Pagados</h3>
        <x-filament-tables::table :records="$pagados" />
    </div>

    <!-- Tabla de Pedidos Pendientes de Pago -->
    <div>
        <h3 class="text-2xl font-semibold text-green-600">Pedidos Pendientes de Pago</h3>
        <x-filament-tables::table :records="$pendientes" />
    </div>
</div>
@endsection
