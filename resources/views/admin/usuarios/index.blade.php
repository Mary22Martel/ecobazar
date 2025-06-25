@extends('layouts.app2')

@section('content')
<div class="px-4 py-6">
  @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
  @endif

  <h1 class="text-2xl font-semibold mb-4">Agricultores</h1>
  <div class="bg-white shadow rounded overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">ID</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Nombre</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Email</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Mercado</th>
          <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Acciones</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($agricultores as $user)
          <tr class="hover:bg-gray-100">
            <td class="px-4 py-2 text-sm text-gray-800">{{ $user->id }}</td>
            <td class="px-4 py-2 text-sm text-gray-800">{{ $user->name }}</td>
            <td class="px-4 py-2 text-sm text-gray-800">{{ $user->email }}</td>
            <td class="px-4 py-2 text-sm text-gray-800">
              {{ optional($user->mercado)->nombre ?? '—' }}
            </td>
            <td class="px-4 py-2 text-sm text-center">
              <a href="{{ route('admin.usuarios.edit', $user) }}"
                 class="text-blue-600 hover:underline">Asignar Mercado</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
              No hay agricultores registrados.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
