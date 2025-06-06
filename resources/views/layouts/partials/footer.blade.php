{{-- resources/views/layouts/partials/footer.blade.php --}}
<footer class="bg-gray-50 text-gray-700 py-8 px-4 md:py-12 mt-auto">
    <div class="container mx-auto max-w-7xl px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-12">
            <!-- Columna 1 - Logo y descripción -->
            <div class="flex flex-col items-center md:items-start space-y-6">
                <a href="{{ url('/') }}" class="mb-4 transition-transform hover:scale-105">
                    <img src="{{ asset('images/logox.png') }}" alt="Ecobazar Logo" class="w-20 md:w-20">
                </a>
                <p class="text-gray-600 text-center md:text-left text-sm leading-relaxed">
                    Tu mercado en línea para productos frescos y de calidad provenientes de ferias agrícolas locales.<br>
                    ¡Compra directamente de los agricultores!
                </p>
                <div class="flex space-x-5 pt-2">
                    <a href="#" aria-label="Facebook" class="p-2 rounded-full bg-gray-100 hover:bg-green-100 transition-colors duration-300">
                        <i class="fab fa-facebook-f text-gray-600 hover:text-green-600 text-lg"></i>
                    </a>
                    <a href="#" aria-label="Twitter" class="p-2 rounded-full bg-gray-100 hover:bg-green-100 transition-colors duration-300">
                        <i class="fab fa-twitter text-gray-600 hover:text-green-600 text-lg"></i>
                    </a>
                    <a href="#" aria-label="Instagram" class="p-2 rounded-full bg-gray-100 hover:bg-green-100 transition-colors duration-300">
                        <i class="fab fa-instagram text-gray-600 hover:text-green-600 text-lg"></i>
                    </a>
                    <a href="#" aria-label="LinkedIn" class="p-2 rounded-full bg-gray-100 hover:bg-green-100 transition-colors duration-300">
                        <i class="fab fa-linkedin-in text-gray-600 hover:text-green-600 text-lg"></i>
                    </a>
                </div>

        <!-- Divider y Copyright -->
        <div class="mt-12 pt-8 border-t border-gray-200">
            <div class="flex flex-col items-center">
                <p class="text-gray-600 text-sm text-center">
                    © {{ date('Y') }} Ecobazar. Todos los derechos reservados<br class="md:hidden">
                    <span class="hidden md:inline"> | </span>
                    <a href="#" class="hover:text-green-600 transition-colors duration-200">Políticas de Privacidad</a> | 
                    <a href="#" class="hover:text-green-600 transition-colors duration-200">Términos de Servicio</a>
                </p>
            </div>
        </div>
    </div>
</footer>>
            </div>

            <!-- Columna 2 - Categorías -->
            <div class="flex flex-col items-center md:items-start space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Categorías</h3>
                <ul class="grid grid-cols-2 gap-3 text-center md:text-left">
                    @php
                        $categories = [
                            ['name' => 'Todo', 'route' => 'tienda'],
                            ['name' => 'Vegetales', 'route' => 'tienda'],
                            ['name' => 'Fruta', 'route' => 'tienda'],
                            ['name' => 'Verduras', 'route' => 'tienda'],
                            ['name' => 'Legumbres', 'route' => 'tienda'],
                            ['name' => 'Queso', 'route' => 'tienda'],
                            ['name' => 'Tubérculos', 'route' => 'tienda'],
                            ['name' => 'Granos', 'route' => 'tienda']
                        ];
                    @endphp
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route($category['route']) }}" 
                               class="text-gray-600 hover:text-green-600 text-sm transition-colors duration-200 hover:pl-1">
                                {{ $category['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Columna 3 - Enlaces útiles -->
            <div class="flex flex-col items-center md:items-start space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Enlaces Útiles</h3>
                <ul class="space-y-3 text-center md:text-left">
                    @php
                        $links = [
                            ['name' => 'Inicio', 'route' => '/'],
                            ['name' => 'Sobre Nosotros', 'route' => 'nosotros'],
                            ['name' => 'Tienda', 'route' => 'tienda'],
                            ['name' => 'Contacto', 'route' => '#']
                        ];
                    @endphp
                    @foreach($links as $link)
                        <li>
                            <a href="{{ $link['route'] === '/' ? url('/') : ($link['route'] === '#' ? '#' : route($link['route'])) }}" 
                               class="text-gray-600 hover:text-green-600 text-sm transition-colors duration-200">
                                {{ $link['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Columna 4 - Contacto -->
            <div class="flex flex-col items-center md:items-start space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Contáctanos</h3>
                <ul class="space-y-4">
                    <li class="flex items-center space-x-3 group">
                        <div class="p-2 bg-green-100 rounded-full">
                            <i class="fas fa-phone-alt text-green-600 text-sm"></i>
                        </div>
                        <a href="tel:+51999999999" 
                           class="text-gray-600 hover:text-green-600 text-sm transition-colors duration-200">
                            +51 999 999 999
                        </a>
                    </li>
                    <li class="flex items-center space-x-3 group">
                        <div class="p-2 bg-green-100 rounded-full">
                            <i class="fas fa-envelope text-green-600 text-sm"></i>
                        </div>
                        <a href="mailto:contacto@ecobazar.com" 
                           class="text-gray-600 hover:text-green-600 text-sm transition-colors duration-200">
                            contacto@.com
                        </a>
                    </li>
                    <li class="flex items-center space-x-3 group">
                        <div class="p-2 bg-green-100 rounded-full">
                            <i class="fas fa-map-marker-alt text-green-600 text-sm"></i>
                        </div>
                        <span class="text-gray-600 text-sm">
                            Huánuco, Perú
                        </span>
                    </li>
                </ul>
            </div>
        </div