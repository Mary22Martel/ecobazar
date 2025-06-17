<!-- Footer Component -->
<footer class="bg-gray-900 text-gray-300 pt-12 pb-6 mt-16">
    <div class="container mx-auto max-w-6xl px-4">
        
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 mb-8">
            
            <!-- Logo and Description -->
            <div class="text-center lg:text-left">
                <div class="mb-4">
                    <img src="{{ asset('images/logox.png') }}" 
                         alt="Punto Verde Logo" 
                         class="w-12 h-12 mx-auto lg:mx-0 mb-3">
                    <h3 class="text-lg font-bold text-white mb-2">
                        Punto Verde Agroecológico
                    </h3>
                    <p class="text-sm text-gray-400 leading-relaxed max-w-xs mx-auto lg:mx-0">
                        Feria Agrícola Sabatina en Amarilis - Huánuco. 
                        14 productores agroecológicos de 3 provincias.
                    </p>
                </div>
                
                <!-- Social Links -->
                <div class="flex justify-center lg:justify-start space-x-3">
                    <a href="https://www.facebook.com/islasdepazperu" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="w-9 h-9 bg-gray-800 hover:bg-blue-600 rounded-full flex items-center justify-center transition-all duration-300 transform hover:scale-110"
                       aria-label="Facebook">
                        <i class="fab fa-facebook-f text-white text-sm"></i>
                    </a>
                    <a href="https://www.instagram.com/puntoverde.huanuco/" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="w-9 h-9 bg-gray-800 hover:bg-pink-600 rounded-full flex items-center justify-center transition-all duration-300 transform hover:scale-110"
                       aria-label="Instagram">
                        <i class="fab fa-instagram text-white text-sm"></i>
                    </a>
                    <a href="mailto:ong_idpp@islasdepazperu.org" 
                       class="w-9 h-9 bg-gray-800 hover:bg-green-600 rounded-full flex items-center justify-center transition-all duration-300 transform hover:scale-110"
                       aria-label="Correo electrónico">
                        <i class="fas fa-envelope text-white text-sm"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="text-center lg:text-left">
                <h4 class="font-semibold text-white mb-4 text-sm flex items-center justify-center lg:justify-start">
                    <i class="fas fa-compass text-green-400 mr-2"></i>
                    Navegación
                </h4>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ url('/') }}" 
                           class="text-sm text-gray-400 hover:text-green-400 transition-colors duration-200 flex items-center justify-center lg:justify-start hover:translate-x-1 transform">
                            <i class="fas fa-home text-xs mr-2"></i>Inicio
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('nosotros') }}" 
                           class="text-sm text-gray-400 hover:text-green-400 transition-colors duration-200 flex items-center justify-center lg:justify-start hover:translate-x-1 transform">
                            <i class="fas fa-users text-xs mr-2"></i>Nosotros
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tienda') }}" 
                           class="text-sm text-gray-400 hover:text-green-400 transition-colors duration-200 flex items-center justify-center lg:justify-start hover:translate-x-1 transform">
                            <i class="fas fa-store text-xs mr-2"></i>Tienda
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Products -->
            <div class="text-center lg:text-left">
                <h4 class="font-semibold text-white mb-4 text-sm flex items-center justify-center lg:justify-start">
                    <i class="fas fa-leaf text-green-400 mr-2"></i>
                    Productos
                </h4>
                <ul class="grid grid-cols-2 gap-2">
                    <li>
                        <a href="{{ route('tienda') }}" 
                           class="text-sm text-gray-400 hover:text-green-400 transition-colors duration-200 flex items-center hover:translate-x-1 transform">
                            <i class="fas fa-seedling text-xs mr-1"></i>Vegetales
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tienda') }}" 
                           class="text-sm text-gray-400 hover:text-green-400 transition-colors duration-200 flex items-center hover:translate-x-1 transform">
                            <i class="fas fa-apple-alt text-xs mr-1"></i>Frutas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tienda') }}" 
                           class="text-sm text-gray-400 hover:text-green-400 transition-colors duration-200 flex items-center hover:translate-x-1 transform">
                            <i class="fas fa-carrot text-xs mr-1"></i>Verduras
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tienda') }}" 
                           class="text-sm text-gray-400 hover:text-green-400 transition-colors duration-200 flex items-center hover:translate-x-1 transform">
                            <i class="fas fa-cheese text-xs mr-1"></i>Lácteos
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="text-center lg:text-left">
                <h4 class="font-semibold text-white mb-4 text-sm flex items-center justify-center lg:justify-start">
                    <i class="fas fa-info-circle text-green-400 mr-2"></i>
                    Información
                </h4>
                <ul class="space-y-3">
                    <li class="flex items-start justify-center lg:justify-start space-x-2">
                        <div class="w-6 h-6 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-map-marker-alt text-white text-xs"></i>
                        </div>
                        <div class="text-sm text-gray-400 text-center lg:text-left">
                            <span class="text-white font-medium block">Ubicación:</span>
                            Segundo Parque de Paucarbambilla<br>
                            Amarilis, Huánuco
                        </div>
                    </li>
                    <li class="flex items-start justify-center lg:justify-start space-x-2">
                        <div class="w-6 h-6 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-clock text-white text-xs"></i>
                        </div>
                        <div class="text-sm text-gray-400 text-center lg:text-left">
                            <span class="text-white font-medium block">Horarios:</span>
                            Sábados 6:30 AM - 12:00 PM
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-700 pt-6">
            
            <!-- Bottom Info -->
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                
                <!-- Copyright -->
                <div class="text-center sm:text-left">
                    <p class="text-sm text-gray-400">
                        © 2025 
                        <span class="text-green-400 font-semibold">Punto Verde Agroecológico</span> 
                        - Amarilis, Huánuco
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Una iniciativa de la Asociación de Productores Agroecológicos
                    </p>
                </div>
                
                <!-- Additional Links -->
                <div class="flex items-center space-x-4 text-xs">
                    <a href="#" class="text-gray-400 hover:text-green-400 transition-colors duration-200">
                        Política de Privacidad
                    </a>
                    <span class="text-gray-600">|</span>
                    <a href="#" class="text-gray-400 hover:text-green-400 transition-colors duration-200">
                        Términos de Uso
                    </a>
                </div>
            </div>
            
            <!-- Eco Message -->
            <div class="mt-4 text-center">
                <div class="inline-flex items-center px-4 py-2 bg-green-900 bg-opacity-50 rounded-full">
                    <i class="fas fa-leaf text-green-400 mr-2 text-sm"></i>
                    <span class="text-xs text-green-200 font-medium">
                        🌱 Productos 100% agroecológicos, sin químicos dañinos
                    </span>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="back-to-top" 
        class="fixed bottom-20 left-4 w-10 h-10 bg-green-600 hover:bg-green-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 opacity-0 pointer-events-none z-30"
        aria-label="Volver arriba">
    <i class="fas fa-chevron-up text-sm"></i>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Back to top functionality
    const backToTopBtn = document.getElementById('back-to-top');
    
    if (backToTopBtn) {
        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
                backToTopBtn.classList.add('opacity-100');
            } else {
                backToTopBtn.classList.add('opacity-0', 'pointer-events-none');
                backToTopBtn.classList.remove('opacity-100');
            }
        });
        
        // Smooth scroll to top
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});
</script>