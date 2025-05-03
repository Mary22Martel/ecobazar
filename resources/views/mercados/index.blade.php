@extends('layouts.app') {{-- O tu layout público principal --}}

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
  <h1 class="text-3xl font-bold mb-6">Ferias y Mercados Disponibles</h1>

  @if($mercados->isEmpty())
    <p class="text-gray-600">No hay ferias activas en este momento.</p>
  @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach($mercados as $m)
        <a href="{{ route('mercados.tienda', $m) }}"
           class="block bg-white shadow rounded-lg p-6 hover:shadow-lg transition">
          <h2 class="text-xl font-semibold">{{ $m->nombre }}</h2>
          @if($m->zona)
            <p class="mt-1 text-gray-600">Zona: {{ $m->zona }}</p>
          @endif
          <span class="mt-4 inline-block text-blue-600 hover:underline">
            Ver productos
          </span>
        </a>
      @endforeach
    </div>
  @endif
</div>
@endsection
