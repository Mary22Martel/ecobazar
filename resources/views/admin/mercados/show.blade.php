@extends('layouts.app2')

@section('content')
<div class="px-4 py-6">
  <h1 class="text-2xl font-semibold mb-4">Detalle de Mercado</h1>

  <div class="bg-white shadow rounded p-6 mb-6">
    <p><strong>ID:</strong> {{ $mercado->id }}</p>
    <p class="mt-2"><strong>Nombre:</strong> {{ $mercado->nombre }}</p>
    <p class="mt-2"><strong>Zona:</strong> {{ $mercado->zona }}</p>
    <p class="mt-2"><strong>Creado:</strong> {{ $mercado->created_at->format('d/m/Y H:i') }}</p>
    <p class="mt-2"><strong>Última actualización:</strong> {{ $mercado->updated_at->format('d/m/Y H:i') }}</p>
  </div>

  <a href="{{ route('admin.mercados.index') }}"
     class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
    Volver a la lista
  </a>
</div>
@endsection
