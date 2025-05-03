@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">

  {{-- Enlace de vuelta al listado de ferias --}}
  <a href="{{ route('mercados.index') }}"
     class="inline-block mb-6 text-blue-600 hover:underline">
    ← Volver a Ferias
  </a>

  <h1 class="text-3xl font-bold mb-4">
    {{ $mercado->nombre }}@if($mercado->zona) ({{ $mercado->zona }})@endif
  </h1>

  <div class="flex">
    <!-- Sidebar: categorías + productores -->
    <div class="w-1/5 bg-white shadow-lg p-4 mr-6">
      <h2 class="text-xl font-bold mb-4">Categorías</h2>
      <ul class="space-y-2">
        <li>
          <a href="{{ route('mercados.tienda', $mercado) }}"
             class="block px-4 py-2 rounded-lg {{ request()->is("mercados/{$mercado->id}/tienda") ? 'bg-green-500 text-white' : 'text-gray-700 hover:bg-gray-200' }}">
            Todo
          </a>
        </li>
        @foreach($categorias as $cat)
          <li>
            <a href="{{ route('productos.filtrarPorCategoria', $cat->id) }}"
               class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-200">
              {{ $cat->nombre }}
            </a>
          </li>
        @endforeach
      </ul>

      <h2 class="text-xl font-bold mb-4 mt-8">Productores</h2>
      <ul class="space-y-2">
        @foreach($productores as $prod)
          <li>
            <a href="{{ route('productos.filtrarPorProductor', $prod->id) }}"
               class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-200">
              {{ $prod->name }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>

    <!-- Listado de productos filtrados -->
    <div class="flex-1">
      @if($productos->isEmpty())
        <p class="text-gray-600">No hay productos disponibles en esta feria.</p>
      @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          @foreach ($productos as $producto)
            <div class="bg-white shadow-lg rounded-lg p-4 flex flex-col transition hover:shadow-2xl">
              <a href="{{ route('producto.show', $producto->id) }}">
                @if($producto->imagen)
                  <img src="{{ asset('storage/' . $producto->imagen) }}"
                       alt="{{ $producto->nombre }}"
                       class="mb-4 w-full h-48 object-cover rounded-lg">
                @else
                  <div class="mb-4 w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                    Sin imagen
                  </div>
                @endif
              </a>

              <a href="{{ route('producto.show', $producto->id) }}">
                <h3 class="font-bold text-lg">{{ $producto->nombre }}</h3>
              </a>
              <p class="text-gray-500">S/{{ number_format($producto->precio, 2) }}</p>
              <p class="text-sm text-gray-400">Disponibles: {{ $producto->cantidad_disponible }}</p>

              <form class="add-to-cart-form mt-auto" action="{{ route('carrito.add', $producto->id) }}" method="POST">
                @csrf
                <input type="hidden" name="cantidad" value="1">
                <button type="submit"
                        class="bg-green-500 text-white px-4 py-2 rounded-lg w-full hover:bg-green-600">
                  Agregar al carrito
                </button>
              </form>
            </div>
          @endforeach
        </div>

        {{-- Paginación --}}
        <div class="mt-6">
          {{ $productos->links() }}
        </div>
      @endif
    </div>
  </div>

  <!-- Botón fijo de ver carrito -->
  <div class="fixed bottom-4 right-4">
    <a href="{{ route('carrito.index') }}"
       class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-600">
      Ver carrito
    </a>
  </div>
</div>
@endsection

@section('scripts')
  {{-- Copia aquí el script de búsqueda AJAX y carrito de tu tienda.blade.php original --}}
  @parent
@endsection
