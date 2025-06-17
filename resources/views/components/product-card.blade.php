<!-- Product Card Component -->
<article class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100 hover:border-green-200 transform hover:-translate-y-1">
    
    <!-- Product Image -->
    <div class="relative overflow-hidden aspect-square">
        <a href="{{ route('producto.show', $producto->id) }}" class="block h-full">
            @if($producto->imagen)
                <img src="{{ asset('storage/' . $producto->imagen) }}" 
                     alt="{{ $producto->nombre }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                     loading="lazy"
                     onerror="this.onerror=null; this.src='{{ asset('images/default-product.png') }}'; this.parentElement.parentElement.querySelector('.image-fallback').style.display='flex';">
            @else
                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center image-fallback">
                    <i class="fas fa-seedling text-3xl text-gray-400"></i>
                </div>
            @endif
        </a>
        
        <!-- Stock Status Badge -->
        @if($producto->cantidad_disponible <= 5 && $producto->cantidad_disponible > 0)
            <div class="absolute top-2 left-2 bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-semibold shadow-md">
                ¡Solo {{ $producto->cantidad_disponible }}!
            </div>
        @elseif($producto->cantidad_disponible == 0)
            <div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold shadow-md">
                Agotado
            </div>
        @elseif($producto->cantidad_disponible > 20)
            <div class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold shadow-md">
                Disponible
            </div>
        @endif

        <!-- Favorite Button (Future feature) -->
        <button class="absolute top-2 right-2 w-8 h-8 bg-white bg-opacity-90 hover:bg-opacity-100 rounded-full flex items-center justify-center text-gray-400 hover:text-red-500 transition-all duration-200 opacity-0 group-hover:opacity-100">
            <i class="fas fa-heart text-sm"></i>
        </button>
    </div>

    <!-- Product Info -->
    <div class="p-3 sm:p-4">
        
        <!-- Product Name -->
        <a href="{{ route('producto.show', $producto->id) }}" class="block mb-2">
            <h3 class="font-semibold text-sm sm:text-base text-gray-800 group-hover:text-green-600 transition-colors line-clamp-2 leading-tight">
                {{ $producto->nombre }}
            </h3>
        </a>
        
        <!-- Producer Info -->
        @if($producto->user)
            <p class="text-xs text-gray-500 mb-2 flex items-center">
                <i class="fas fa-user-tie mr-1"></i>
                {{ $producto->user->name }}
            </p>
        @endif
        
        <!-- Price Section -->
        <div class="flex items-center justify-between mb-3">
            <div class="flex flex-col">
                <span class="text-lg sm:text-xl font-bold text-green-600">
                    S/{{ number_format($producto->precio, 2) }}
                </span>
                @if($producto->medida)
                    <span class="text-xs text-gray-600 font-medium">
                        por {{ $producto->medida->nombre }}
                    </span>
                @endif
            </div>
            
            <!-- Stock Info -->
            <div class="text-right">
                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                    {{ $producto->cantidad_disponible }} 
                    @if($producto->medida)
                        {{ $producto->medida->nombre }}{{ $producto->cantidad_disponible > 1 && $producto->medida->nombre != 'Unidad' ? 's' : '' }}
                    @else
                        unidades
                    @endif
                </span>
            </div>
        </div>

        <!-- Add to Cart Section -->
        @if($producto->cantidad_disponible > 0)
            <form class="add-to-cart-form" action="{{ route('carrito.add', $producto->id) }}" method="POST" data-product-id="{{ $producto->id }}">
                @csrf
                
                <!-- Quantity Selector -->
                <div class="flex items-center justify-between mb-3 p-2 bg-gray-50 rounded-lg">
                    <label class="text-xs font-medium text-gray-700">
                        Cantidad
                        @if($producto->medida && $producto->medida->nombre != 'Unidad')
                            <span class="text-gray-500">({{ $producto->medida->nombre }}s)</span>
                        @endif
                    </label>
                    
                    <div class="flex items-center space-x-2">
                        <button type="button" 
                                class="quantity-btn minus-btn w-7 h-7 rounded-md bg-white border border-gray-300 hover:bg-gray-100 transition-colors flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                                data-action="decrease">
                            <i class="fas fa-minus text-xs text-gray-600"></i>
                        </button>
                        
                        <input type="number" 
                               name="cantidad" 
                               class="quantity-input w-12 px-2 py-1 text-center border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm" 
                               value="1" 
                               min="1" 
                               max="{{ $producto->cantidad_disponible }}"
                               step="1">
                        
                        <button type="button" 
                                class="quantity-btn plus-btn w-7 h-7 rounded-md bg-white border border-gray-300 hover:bg-gray-100 transition-colors flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                                data-action="increase">
                            <i class="fas fa-plus text-xs text-gray-600"></i>
                        </button>
                    </div>
                </div>

                <!-- Total Price -->
                <div class="text-center mb-3 p-2 bg-green-50 rounded-lg">
                    <span class="text-xs text-gray-600">Total: </span>
                    <span class="total-price text-sm font-bold text-green-600" 
                          data-unit-price="{{ $producto->precio }}">
                        S/{{ number_format($producto->precio, 2) }}
                    </span>
                </div>

                <!-- Add to Cart Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-200 transform hover:scale-105 flex items-center justify-center space-x-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    <i class="fas fa-cart-plus text-sm"></i>
                    <span>Agregar al Carrito</span>
                </button>
            </form>
        @else
            <!-- Out of Stock -->
            <div class="text-center py-3">
                <button disabled 
                        class="w-full bg-gray-400 text-white font-medium py-2.5 px-4 rounded-lg cursor-not-allowed flex items-center justify-center space-x-2 text-sm">
                    <i class="fas fa-times text-sm"></i>
                    <span>Agotado</span>
                </button>
            </div>
        @endif

        <!-- Quick View Button -->
        <button type="button"
                class="w-full mt-2 text-gray-600 hover:text-green-600 text-xs font-medium py-2 transition-colors duration-200 border border-gray-200 hover:border-green-300 rounded-lg quick-view-btn"
                data-product-id="{{ $producto->id }}"
                onclick="showQuickView({{ $producto->id }})">
            <i class="fas fa-eye mr-1"></i>
            Vista rápida
        </button>
    </div>
</article>