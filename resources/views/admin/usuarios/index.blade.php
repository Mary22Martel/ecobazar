{{-- resources/views/admin/usuarios/index.blade.php --}}
@extends('layouts.app2')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 md:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6 md:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Gestión de Usuarios</h1>
                    <p class="mt-2 text-sm md:text-base text-gray-600">Administra las cuentas de agricultores y repartidores</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('admin.usuarios.crear') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Crear Usuario
                    </a>
                </div>
            </div>
        </div>

        <!-- Mensajes -->
        @if(session('success'))
            <div class="mb-4 md:mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 md:mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.081 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
            <div class="bg-white p-4 md:p-6 rounded-lg shadow border">
                <div class="flex flex-col sm:flex-row sm:items-center">
                    <div class="p-2 md:p-3 rounded-md bg-blue-50 mb-2 sm:mb-0 self-start">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="sm:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">Total Usuarios</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $usuarios->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 md:p-6 rounded-lg shadow border">
                <div class="flex flex-col sm:flex-row sm:items-center">
                    <div class="p-2 md:p-3 rounded-md bg-green-50 mb-2 sm:mb-0 self-start">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="sm:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">Agricultores</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $usuarios->where('role', 'agricultor')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 md:p-6 rounded-lg shadow border">
                <div class="flex flex-col sm:flex-row sm:items-center">
                    <div class="p-2 md:p-3 rounded-md bg-blue-50 mb-2 sm:mb-0 self-start">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                    </div>
                    <div class="sm:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">Repartidores</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $usuarios->where('role', 'repartidor')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="mb-6 bg-white rounded-lg shadow p-4">
            <div class="flex flex-wrap gap-2">
                <button type="button" class="px-3 py-2 text-sm font-medium rounded-md bg-green-600 text-white" data-filter="all">
                    Todos
                </button>
                <button type="button" class="px-3 py-2 text-sm font-medium rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200" data-filter="agricultor">
                    Agricultores
                </button>
                <button type="button" class="px-3 py-2 text-sm font-medium rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200" data-filter="repartidor">
                    Repartidores
                </button>
            </div>
        </div>

        <!-- Lista de Usuarios -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 md:px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg md:text-xl font-semibold text-gray-900">Lista de Usuarios</h2>
                <p class="text-sm text-gray-600 mt-1">Gestiona todos los usuarios del sistema</p>
            </div>
            
            <div class="divide-y divide-gray-200">
                @if(isset($usuarios) && count($usuarios) > 0)
                    @foreach($usuarios as $usuario)
                        <div class="p-4 md:p-6" data-role="{{ $usuario->role }}">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 {{ $usuario->role === 'agricultor' ? 'bg-green-100' : 'bg-blue-100' }} rounded-full flex items-center justify-center">
                                            <span class="{{ $usuario->role === 'agricultor' ? 'text-green-600' : 'text-blue-600' }} font-semibold text-sm">
                                                {{ strtoupper(substr($usuario->name, 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-base md:text-lg font-medium text-gray-900 truncate">{{ $usuario->name }}</h3>
                                        <p class="text-sm text-gray-600 truncate">{{ $usuario->email }}</p>
                                        <div class="mt-1 flex items-center space-x-4">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $usuario->role === 'agricultor' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($usuario->role === 'agricultor')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                                    @endif
                                                </svg>
                                                {{ ucfirst($usuario->role) }}
                                            </span>
                                            @if($usuario->telefono)
                                                <span class="text-xs text-gray-500">{{ $usuario->telefono }}</span>
                                            @else
                                                <span class="text-xs text-gray-400">Sin teléfono</span>
                                            @endif
                                            <span class="text-xs text-gray-500">{{ $usuario->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                    <button 
                                        onclick="abrirModalEditar('{{ $usuario->id }}', '{{ $usuario->name }}', '{{ $usuario->email }}', '{{ $usuario->telefono }}')"
                                        class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Editar
                                    </button>
                                    
                                    @if($usuario->role === 'agricultor')
                                        <a href="{{ route('admin.pagos.detalle-agricultor', $usuario->id) }}" 
                                           class="inline-flex items-center justify-center px-4 py-2 border border-green-300 text-sm font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                            <span class="hidden sm:inline">Ver Productos</span>
                                            <span class="sm:hidden">Productos</span>
                                        </a>
                                    @endif
                                    
                                    @if($usuario->role === 'repartidor')
                                        <a href="{{ route('admin.repartidores.detalle', $usuario->id) }}" 
                                           class="inline-flex items-center justify-center px-4 py-2 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                            </svg>
                                            <span class="hidden sm:inline">Ver Entregas</span>
                                            <span class="sm:hidden">Entregas</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay usuarios registrados</h3>
                        <p class="text-gray-600 mb-4">Comienza creando el primer agricultor o repartidor.</p>
                        <a href="{{ route('admin.usuarios.crear') }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Crear Primer Usuario
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edición -->
<div id="modalEditar" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 p-4">
    <div class="flex items-center justify-center min-h-full">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="p-4 md:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 id="modalTitleEditar" class="text-lg font-medium text-gray-900">Editar Usuario</h3>
                    <button onclick="cerrarModalEditar()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="formEditar" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                            <input type="text" id="editName" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                        </div>
                        
                        <!-- <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="editEmail" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                        </div> -->
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input type="text" id="editTelefono" name="telefono" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Opcional">
                        </div>
                        
                        <!-- <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                            <input type="password" id="editPassword" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Dejar vacío para no cambiar">
                            <p class="text-xs text-gray-500 mt-1">Solo completa si quieres cambiar la contraseña</p>
                        </div> -->
                    </div>
                    
                    <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 mt-6">
                        <button type="button" onclick="cerrarModalEditar()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Filtros
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('[data-filter]');
    const userRows = document.querySelectorAll('[data-role]');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Actualizar botones activos
            filterButtons.forEach(btn => {
                btn.classList.remove('bg-green-600', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            });
            this.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            this.classList.add('bg-green-600', 'text-white');
            
            // Filtrar filas
            userRows.forEach(row => {
                const role = row.dataset.role;
                if (filter === 'all' || filter === role) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});

// Modal de edición
function abrirModalEditar(id, nombre, email, telefono) {
    document.getElementById('modalTitleEditar').textContent = 'Editar Usuario: ' + nombre;
    document.getElementById('formEditar').action = '{{ route("admin.usuarios.actualizar", ":id") }}'.replace(':id', id);
    document.getElementById('editName').value = nombre;
    // document.getElementById('editEmail').value = email; // Campo comentado
    document.getElementById('editTelefono').value = telefono || '';
    // document.getElementById('editPassword').value = ''; // Campo comentado
    
    document.getElementById('modalEditar').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function cerrarModalEditar() {
    document.getElementById('modalEditar').classList.add('hidden');
    document.body.style.overflow = '';
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalEditar').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalEditar();
    }
});
</script>
@endsection