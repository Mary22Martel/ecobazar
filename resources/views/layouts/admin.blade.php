<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title','Panel de Administración')</title>
  <!-- Tu CSS compilado (Tailwind) -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
  <div class="flex">

    {{-- Sidebar --}}
    <nav class="w-64 bg-white h-screen shadow">
      <div class="p-6 text-lg font-bold border-b">Admin</div>
      <ul class="mt-4">
        <li><a href="{{ route('admin.mercados.index') }}" class="block px-4 py-2 hover:bg-gray-200">Mercados</a></li>
        <li><a href="{{ route('admin.usuarios.index') }}" class="block px-4 py-2 hover:bg-gray-200">Agricultores</a></li>
        <li><a href="{{ route('admin.canastas.index') }}" class="block px-4 py-2 hover:bg-gray-200">Canastas</a></li>
        <!-- Añade aquí más enlaces de tu menú admin -->
      </ul>
    </nav>

    {{-- Contenido principal --}}
    <main class="flex-1 p-6">
      @yield('content')
    </main>
  </div>

  <!-- Tu JS compilado -->
  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
