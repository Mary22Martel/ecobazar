@extends('layouts.app2')

@section('content')
<div class="px-4 py-6">
  <h1 class="text-2xl font-semibold mb-4">
    Asignar Mercado a: {{ $usuario->name }}
  </h1>

  @if($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.usuarios.update', $usuario) }}"
        method="POST"
        class="bg-white shadow rounded p-6 space-y-4">
    @csrf @method('PUT')

    <div>
      <label for="mercado_id" class="block text-gray-700 font-medium">
        Mercado
      </label>
      <select name="mercado_id" id="mercado_id" required
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        <option value="">-- Selecciona un mercado --</option>
        @foreach($mercados as $m)
          <option value="{{ $m->id }}"
            {{ old('mercado_id', $usuario->mercado_id) == $m->id ? 'selected' : '' }}>
            {{ $m->nombre }} ({{ $m->zona }})
          </option>
        @endforeach
      </select>
    </div>

    <div class="flex items-center space-x-4">
      <button type="submit"
              class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
        Guardar
      </button>
      <a href="{{ route('admin.usuarios.index') }}"
         class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
        Cancelar
      </a>
    </div>
  </form>
</div>
@endsection
