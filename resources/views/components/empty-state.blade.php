<!-- Empty State Component -->
<div class="text-center py-12 sm:py-16 bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="max-w-md mx-auto px-4">
        <!-- Icon -->
        <div class="mb-4">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-seedling text-2xl sm:text-3xl text-gray-400"></i>
            </div>
        </div>
        
        <!-- Title -->
        <h3 class="text-lg sm:text-xl font-semibold text-gray-700 mb-2">
            @if(request()->has('query'))
                No se encontraron productos
            @else
                No hay productos disponibles
            @endif
        </h3>
        
        <!-- Description -->
        <p class="text-sm text-gray-500 mb-6 leading-relaxed">
            @if(request()->has('query'))
                No encontramos productos que coincidan con tu búsqueda "{{ request()->query }}". Intenta con otros términos o explora nuestras categorías.
            @elseif(request()->has('categoria'))
                No hay productos disponibles en esta categoría en este momento.
            @elseif(request()->has('productor'))
                Este productor no tiene productos disponibles actualmente.
            @else
                No hay productos disponibles en este momento. Vuelve pronto para ver nuestros productos frescos.
            @endif
        </p>
        
        <!-- Actions -->
        <div class="space-y-3">
            @if(request()->has('query') || request()->has('categoria') || request()->has('productor'))
                <!-- Si hay filtros aplicados, mostrar botón para limpiar -->
                <a href="{{ route('tienda') }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-store mr-2"></i>
                    Ver todos los productos
                </a>
                
                <div class="text-center">
                    <button onclick="clearFilters()" 
                            class="text-sm text-gray-500 hover:text-gray-700 underline transition-colors duration-200">
                        Limpiar filtros
                    </button>
                </div>
            @else
                <!-- Si no hay filtros, mostrar acciones generales -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('tienda') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-refresh mr-2"></i>
                        Actualizar página
                    </a>
                    
                    @if(isset($categorias) && $categorias->count() > 0)
                        <button onclick="showCategories()" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-th-large mr-2"></i>
                            Ver categorías
                        </button>
                    @endif
                </div>
            @endif
        </div>

        <!-- Suggestions -->
        <div class="mt-8 p-4 bg-green-50 rounded-lg">
            <h4 class="text-sm font-medium text-green-800 mb-2">💡 Sugerencias:</h4>
            <ul class="text-xs text-green-700 space-y-1 text-left">
                @if(request()->has('query'))
                    <li>• Revisa la ortografía de tu búsqueda</li>
                    <li>• Usa términos más generales</li>
                    <li>• Intenta con sinónimos</li>
                @endif
                <li>• Explora nuestras categorías populares</li>
                <li>• Contacta con nuestros productores</li>
                <li>• Vuelve más tarde para ver nuevos productos</li>
            </ul>
        </div>

        <!-- Contact Info -->
        @if(!request()->has('query'))
        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <h4 class="text-sm font-medium text-blue-800 mb-2">📞 ¿Necesitas ayuda?</h4>
            <p class="text-xs text-blue-700">
                Contacta a nuestros agricultores o administradores para conocer sobre próximos productos disponibles.
            </p>
        </div>
        @endif
    </div>
</div>

<!-- Categories Modal (Hidden by default) -->
@if(isset($categorias) && $categorias->count() > 0)
<div id="categories-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full max-h-[80vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Categorías Disponibles</h3>
                <button onclick="hideCategories()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <div class="space-y-2">
                @foreach($categorias as $categoria)
                    <a href="{{ route('productos.filtrarPorCategoria', $categoria->id) }}" 
                       class="block p-3 bg-gray-50 hover:bg-green-50 rounded-lg transition-colors">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-800">{{ $categoria->nombre }}</span>
                            <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded-full">
                                {{ $categoria->productos_count ?? $categoria->productos->count() }} productos
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<script>
function clearFilters() {
    // Clear all filters and search parameters
    const url = new URL(window.location);
    url.search = '';
    window.location.href = url.toString();
}

function showCategories() {
    const modal = document.getElementById('categories-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
}

function hideCategories() {
    const modal = document.getElementById('categories-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
}

// Close modal on background click
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('categories-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideCategories();
            }
        });
    }
});

// Handle search suggestions
function searchSuggestion(term) {
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.value = term;
        searchInput.form.submit();
    } else {
        // Fallback: redirect to search
        window.location.href = `/buscar?query=${encodeURIComponent(term)}`;
    }
}

// Popular search terms (you can customize these)
const popularSearches = [
    'verduras',
    'frutas',
    'orgánico',
    'tomate',
    'lechuga',
    'papa',
    'cebolla',
    'zanahoria'
];

// Show popular searches if no results
document.addEventListener('DOMContentLoaded', function() {
    const hasQuery = {{ request()->has('query') ? 'true' : 'false' }};
    
    if (hasQuery && popularSearches.length > 0) {
        // Add popular searches section
        const suggestionsContainer = document.querySelector('.text-green-700.space-y-1');
        if (suggestionsContainer) {
            const popularDiv = document.createElement('div');
            popularDiv.className = 'mt-4 pt-3 border-t border-green-200';
            popularDiv.innerHTML = `
                <p class="text-xs font-medium text-green-800 mb-2">Búsquedas populares:</p>
                <div class="flex flex-wrap gap-1">
                    ${popularSearches.map(term => 
                        `<button onclick="searchSuggestion('${term}')" 
                                 class="text-xs bg-green-100 hover:bg-green-200 text-green-700 px-2 py-1 rounded transition-colors">
                            ${term}
                         </button>`
                    ).join('')}
                </div>
            `;
            suggestionsContainer.parentElement.appendChild(popularDiv);
        }
    }
});
</script>