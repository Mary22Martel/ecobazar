@extends('layouts.app2')

@section('content')
<div class="px-4 py-6">
  <h1 class="text-2xl font-semibold mb-4">Crear Mercado</h1>

  @if($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.mercados.store') }}" method="POST" class="space-y-4 bg-white shadow rounded p-6">
    @csrf
    <div>
      <label for="nombre" class="block text-gray-700 font-medium">Nombre</label>
      <input type="text" name="nombre" id="nombre"
             value="{{ old('nombre') }}"
             required
             class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
    </div>

    <div>
      <label for="zona" class="block text-gray-700 font-medium">Zona</label>
      <input type="text" name="zona" id="zona"
             value="{{ old('zona') }}"
             class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
    </div>

    <div class="flex items-center space-x-4">
      <button type="submit"
              class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
        Guardar
      </button>
      <a href="{{ route('admin.mercados.index') }}"
         class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
        Cancelar
      </a>
    </div>
  </form>
</div>
@endsection
