@extends('layouts.app2')

@section('content')
<div class="container mx-auto mt-8 max-w-4xl px-4">
    <!-- Menú de navegación fijo superior -->
    <div class="bg-white border-2 border-gray-300 rounded-lg p-4 mb-6 shadow sticky top-4 z-10">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">🏠 MI PANEL</h2>
            <div class="flex space-x-2">
                <a href="{{ route('agricultor.pedidos_pendientes') }}" 
                   class="bg-orange-500 text-white px-3 py-2 rounded text-sm font-bold hover:bg-orange-600">
                    📦 PENDIENTES
                </a>
                <a href="{{ route('agricultor.pedidos_listos') }}" 
                   class="bg-green-500 text-white px-3 py-2 rounded text-sm font-bold hover:bg-green-600">
                    ✅ LISTOS
                </a>
                <a href="{{ route('agricultor.pagos') }}" 
                   class="bg-blue-500 text-white px-3 py-2 rounded text-sm font-bold hover:bg-blue-600">
                    💰 PAGOS
                </a>
            </div>
        </div>
    </div>

    <!-- Saludo simple -->
    <div class="bg-green-600 text-white p-6 rounded-lg mb-8 text-center">
        <h1 class="text-3xl font-bold">¡Hola {{ Auth::user()->name }}!</h1>
        <p class="text-lg mt-2">Bienvenido a tu espacio de trabajo</p>
    </div>

    <!-- Botones principales grandes -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Pedidos por armar -->
        <a href="{{ route('agricultor.pedidos_pendientes') }}" 
           class="bg-white border-4 border-orange-400 text-gray-800 p-8 rounded-lg shadow-lg text-center block hover:shadow-xl hover:scale-105 transition-all duration-200">
            <div class="text-4xl mb-4">📦</div>
            <h2 class="text-2xl font-bold mb-2 text-orange-600">PEDIDOS POR ARMAR</h2>
            @php
                // Pedidos que necesitan preparación
                $pendientesQuery = \App\Models\Order::whereHas('items.product', function($query) {
                    $query->where('user_id', Auth::id());
                })->whereIn('estado', ['pendiente', 'pagado']);
                $pendientes = $pendientesQuery->count();
            @endphp
            @if($pendientes > 0)
            <div class="bg-red-500 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3 animate-pulse">
                <span class="text-2xl font-bold">{{ $pendientes }}</span>
            </div>
            <p class="text-lg text-gray-700">Tienes {{ $pendientes }} pedidos esperando</p>
            @else
            <p class="text-lg text-gray-500">No hay pedidos pendientes</p>
            @endif
        </a>

        <!-- Pedidos listos -->
        <a href="{{ route('agricultor.pedidos_listos') }}" 
           class="bg-white border-4 border-green-400 text-gray-800 p-8 rounded-lg shadow-lg text-center block hover:shadow-xl hover:scale-105 transition-all duration-200">
            <div class="text-4xl mb-4">✅</div>
            <h2 class="text-2xl font-bold mb-2 text-green-600">PEDIDOS LISTOS</h2>
            @php
                // Pedidos ya listos (generan pago)
                $listosQuery = \App\Models\Order::whereHas('items.product', function($query) {
                    $query->where('user_id', Auth::id());
                })->whereIn('estado', ['armado', 'entregado']);
                $listos = $listosQuery->count();
            @endphp
            <p class="text-lg text-gray-700">{{ $listos }} pedidos preparados</p>
        </a>
    </div>

    <!-- Botones secundarios -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Mis pagos -->
        <a href="{{ route('agricultor.pagos') }}" 
           class="bg-white border-2 border-blue-300 text-gray-800 p-6 rounded-lg shadow text-center block hover:shadow-md hover:border-blue-400 transition-all duration-200">
            <div class="text-3xl mb-3">💰</div>
            <h3 class="text-xl font-bold mb-2 text-blue-600">MIS PAGOS</h3>
            <p class="text-sm text-gray-600">Ver dinero ganado</p>
        </a>

        <!-- Mis productos -->
        <a href="{{ route('productos.index') }}" 
           class="bg-white border-2 border-purple-300 text-gray-800 p-6 rounded-lg shadow text-center block hover:shadow-md hover:border-purple-400 transition-all duration-200">
            <div class="text-3xl mb-3">🥕</div>
            <h3 class="text-xl font-bold mb-2 text-purple-600">MIS PRODUCTOS</h3>
            <p class="text-sm text-gray-600">Administrar catálogo</p>
        </a>
    </div>

    <!-- Resumen rápido -->
    @php
        $totalProductos = \App\Models\Product::where('user_id', Auth::id())->count();
    @endphp

    <div class="bg-white border-2 border-gray-300 rounded-lg p-6 shadow">
        <h3 class="text-xl font-bold text-gray-800 mb-4 text-center">📊 RESUMEN DE HOY</h3>
        <div class="grid grid-cols-3 gap-4 text-center">
            <a href="{{ route('agricultor.pedidos_pendientes') }}" 
               class="bg-orange-50 border border-orange-200 p-4 rounded-lg hover:bg-orange-100 transition-colors block">
                <div class="text-3xl font-bold text-orange-600">{{ $pendientes }}</div>
                <div class="text-sm text-orange-800">Por armar</div>
            </a>
            <a href="{{ route('agricultor.pedidos_listos') }}" 
               class="bg-green-50 border border-green-200 p-4 rounded-lg hover:bg-green-100 transition-colors block">
                <div class="text-3xl font-bold text-green-600">{{ $listos }}</div>
                <div class="text-sm text-green-800">Listos</div>
            </a>
            <a href="{{ route('productos.index') }}" 
               class="bg-purple-50 border border-purple-200 p-4 rounded-lg hover:bg-purple-100 transition-colors block">
                <div class="text-3xl font-bold text-purple-600">{{ $totalProductos }}</div>
                <div class="text-sm text-purple-800">Productos</div>
            </a>
        </div>
    </div>

    <!-- Instrucciones simples -->
    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-6 mt-8">
        <h3 class="text-lg font-bold text-blue-800 mb-3">❓ ¿QUÉ HACER?</h3>
        <div class="space-y-2 text-blue-800">
            <p><strong>1.</strong> Revisa si hay "PEDIDOS POR ARMAR" (naranja)</p>
            <p><strong>2.</strong> Prepara los productos con las cantidades exactas</p>
            <p><strong>3.</strong> Marca como "YA ESTÁ LISTO" cuando termines</p>
            <p><strong>4.</strong> Lleva todo a la feria el día acordado</p>
            <p><strong>5.</strong> Revisa tus "PAGOS" cada semana</p>
        </div>
    </div>

</div>
@endsection