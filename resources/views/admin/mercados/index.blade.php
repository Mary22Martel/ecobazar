@extends('layouts.app2')

@section('content')
<div class="px-4 py-6">
  @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
  @endif

  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold">Mercados</h1>
    <a href="{{ route('admin.mercados.create') }}"
       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
      Nuevo Mercado
    </a>
  </div>

  <div class="overflow-x-auto bg-white shadow rounded">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">ID</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Nombre</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Zona</th>
          <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Acciones</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($mercados as $mercado)
          <tr class="hover:bg-gray-100">
            <td class="px-4 py-2 text-sm text-gray-800">{{ $mercado->id }}</td>
            <td class="px-4 py-2 text-sm text-gray-800">{{ $mercado->nombre }}</td>
            <td class="px-4 py-2 text-sm text-gray-800">{{ $mercado->zona }}</td>
            <td class="px-4 py-2 text-sm text-gray-800 text-center space-x-2">
              <a href="{{ route('admin.mercados.show', $mercado) }}"
                 class="text-blue-600 hover:underline">Ver</a>
              <a href="{{ route('admin.mercados.edit', $mercado) }}"
                 class="text-green-600 hover:underline">Editar</a>
              <form action="{{ route('admin.mercados.destroy', $mercado) }}"
                    method="POST" class="inline"
                    onsubmit="return confirm('¿Eliminar mercado?');">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="px-4 py-6 text-center text-gray-500">
              No hay mercados registrados.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
