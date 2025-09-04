@extends('layouts.app2')

@section('content')
<div class="container mx-auto px-3 py-4 max-w-4xl">
    
    <!-- Header responsive -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
            <div class="mb-3 sm:mb-0">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-1 sm:mb-2">➕ NUEVO PRODUCTO</h1>
                <p class="text-green-100 text-base sm:text-lg">Agrega un producto a tu catálogo</p>
            </div>
        </div>
    </div>

    <!-- Navegación de regreso -->
    <div class="mb-4 sm:mb-6">
        <a href="{{ route('productos.index') }}" 
           class="inline-flex items-center text-gray-600 hover:text-green-600 transition-colors font-medium">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a mis productos
        </a>
    </div>

    <!-- Mensajes de error optimizados -->
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-400 rounded-lg p-4 mb-4 sm:mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-2xl">⚠️</span>
                </div>
                <div class="ml-3">
                    <h4 class="text-base font-bold text-red-800">Revisa estos errores:</h4>
                    <ul class="mt-2 text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Formulario responsivo -->
    <form id="productoForm" action="{{ route('productos.store') }}" method="POST" 
          class="bg-white shadow-lg rounded-xl p-4 sm:p-6 lg:p-8 space-y-6" enctype="multipart/form-data">
        @csrf

        <!-- Nombre del Producto -->
        <div>
            <label for="nombre" class="block text-base sm:text-lg font-semibold text-gray-700 mb-2">
                🏷️ Nombre del Producto
            </label>
            <input type="text" name="nombre" id="nombre" 
                   placeholder="Ej: Tomates frescos, Lechuga orgánica..." 
                   value="{{ old('nombre') }}" 
                   class="block w-full p-3 sm:p-4 border border-gray-300 rounded-lg focus:ring-green-400 focus:border-green-400 text-base" 
                   required>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Escribe un nombre claro y descriptivo</p>
        </div>

        <!-- Grid responsive para campos principales -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            <!-- Unidad de medida -->
            <div>
                <label for="medida" class="block text-base sm:text-lg font-semibold text-gray-700 mb-2">
                    📏 ¿Cómo lo vendes?
                </label>
                <select name="medida_id" id="medida" 
                        class="block w-full p-3 sm:p-4 border border-gray-300 rounded-lg focus:ring-green-400 focus:border-green-400 text-base" 
                        required>
                    <option value="">Selecciona una opción</option>
                    @foreach ($medidas as $medida)
                        <option value="{{ $medida->id }}" {{ old('medida_id') == $medida->id ? 'selected' : '' }}>
                            {{ $medida->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Categoría -->
            <div>
                <label for="categoria" class="block text-base sm:text-lg font-semibold text-gray-700 mb-2">
                    📂 Categoría
                </label>
                <select name="categoria_id" id="categoria" 
                        class="block w-full p-3 sm:p-4 border border-gray-300 rounded-lg focus:ring-green-400 focus:border-green-400 text-base" 
                        required>
                    <option value="">Selecciona una categoría</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Precio y Cantidad -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <!-- Precio -->
            <div>
                <label for="precio" class="block text-base sm:text-lg font-semibold text-gray-700 mb-2">
                    💰 Precio por unidad
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">S/</span>
                    <input type="number" name="precio" id="precio" 
                           placeholder="25.50" 
                           value="{{ old('precio') }}" 
                           step="0.01" min="0.01"
                           class="block w-full pl-10 pr-3 py-3 sm:py-4 border border-gray-300 rounded-lg focus:ring-green-400 focus:border-green-400 text-base" 
                           required>
                </div>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Precio que cobrarás por cada unidad</p>
            </div>

            <!-- Cantidad Disponible -->
            <div>
                <label for="cantidad_disponible" class="block text-base sm:text-lg font-semibold text-gray-700 mb-2">
                    📦 ¿Cuánto tienes?
                </label>
                <input type="number" name="cantidad_disponible" id="cantidad_disponible" 
                       placeholder="100" 
                       value="{{ old('cantidad_disponible') }}" 
                       min="0"
                       class="block w-full p-3 sm:p-4 border border-gray-300 rounded-lg focus:ring-green-400 focus:border-green-400 text-base" 
                       required>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Cantidad que tienes disponible para vender</p>
            </div>
        </div>

        <!-- Descripción mejorada con valor predeterminado -->
        <div>
            <label for="descripcion" class="block text-base sm:text-lg font-semibold text-gray-700 mb-2">
                📝 Descripción
            </label>
            <textarea name="descripcion" id="descripcion" rows="4" 
                      placeholder="Escribe una descripción personalizada o usa la descripción predeterminada..." 
                      class="block w-full p-3 sm:p-4 border border-gray-300 rounded-lg focus:ring-green-400 focus:border-green-400 text-base resize-none" 
                      required>{{ old('descripcion') ?: '🌱 Cultivado con dedicación para brindarte frescura y sabor excepcional' }}</textarea>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mt-2 space-y-2 sm:space-y-0">
                <p class="text-xs sm:text-sm text-gray-500">Personaliza la descripción o deja el mensaje predeterminado</p>
                <button type="button" onclick="resetDescription()" 
                        class="text-xs text-green-600 hover:text-green-700 font-medium underline">
                    🔄 Restaurar mensaje predeterminado
                </button>
            </div>
        </div>

        <!-- Imagen del Producto mejorada -->
        <div>
            <label class="block text-base sm:text-lg font-semibold text-gray-700 mb-3">
                📷 Foto del Producto
            </label>
            
            <!-- Botones de cámara responsivos -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                <button type="button" onclick="openCamera()" 
                        class="bg-blue-500 text-white px-4 py-3 rounded-lg hover:bg-blue-600 transition-all font-semibold text-sm sm:text-base">
                    📱 Abrir Cámara
                </button>
                <button type="button" onclick="capturePhoto()" id="captureBtn" 
                        class="bg-green-500 text-white px-4 py-3 rounded-lg hover:bg-green-600 transition-all font-semibold text-sm sm:text-base" 
                        style="display:none;">
                    📸 Tomar Foto
                </button>
                <button type="button" onclick="closeCamera()" id="closeBtn" 
                        class="bg-red-500 text-white px-4 py-3 rounded-lg hover:bg-red-600 transition-all font-semibold text-sm sm:text-base" 
                        style="display:none;">
                    ❌ Cerrar Cámara
                </button>
            </div>

            <!-- Input de archivo alternativo -->
            <div class="mb-4">
                <label class="block w-full">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-green-400 transition-colors cursor-pointer">
                        <div class="text-3xl mb-2">📁</div>
                        <p class="text-sm sm:text-base text-gray-600 font-medium">O selecciona una foto desde tu galería</p>
                    </div>
                    <input type="file" name="imagen" id="imagen" accept="image/*" class="hidden">
                </label>
            </div>
            
            <!-- Contenedor para la cámara -->
            <div id="cameraContainer" style="display:none;" class="mb-4">
                <div class="bg-gray-100 rounded-lg p-4 text-center">
                    <video id="video" class="max-w-full h-auto rounded-lg border border-gray-300" autoplay muted playsinline></video>
                    <canvas id="canvas" style="display:none;"></canvas>
                    <p class="text-sm text-gray-600 mt-2">Apunta la cámara hacia tu producto</p>
                </div>
            </div>
            
            <!-- Preview de la imagen -->
            <div id="imagePreview" style="display:none;" class="mb-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                    <img id="capturedImage" class="max-w-full h-auto rounded-lg border border-gray-300 mx-auto">
                    <p class="text-sm text-green-700 mt-2 font-medium">✅ Imagen capturada correctamente</p>
                    <button type="button" onclick="clearImage()" class="mt-2 text-xs text-gray-500 underline hover:text-gray-700">
                        Tomar otra foto
                    </button>
                </div>
            </div>
            
            <input type="hidden" id="imagen_base64" name="imagen_base64">
        </div>

        <!-- Botones de acción -->
        <div class="pt-4 sm:pt-6 space-y-3 sm:space-y-0 sm:flex sm:space-x-4">
            <button type="submit" id="guardarProducto" 
                    class="w-full sm:flex-1 py-3 sm:py-4 bg-gradient-to-r from-green-500 to-green-600 text-white text-base sm:text-lg font-bold rounded-lg hover:from-green-600 hover:to-green-700 transition-all focus:outline-none focus:ring-4 focus:ring-green-300 focus:ring-opacity-50 transform hover:scale-105">
                💾 Guardar Producto
            </button>
            <a href="{{ route('productos.index') }}" 
               class="w-full sm:w-auto py-3 sm:py-4 px-6 bg-gray-500 text-white text-base sm:text-lg font-bold rounded-lg hover:bg-gray-600 transition-all text-center block">
                ❌ Cancelar
            </a>
        </div>
    </form>

    <!-- Instrucciones adicionales mejoradas -->
    <div class="mt-6 sm:mt-8 space-y-4">
        <!-- Consejos para fotos -->
        <div class="bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl p-4 sm:p-6">
            <h3 class="text-base font-bold text-green-800 mb-3 flex items-center">
                <span class="mr-2">💡</span> Consejos para una buena foto
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm sm:text-base text-green-700">
                <div class="flex items-start space-x-2">
                    <span class="text-green-600">•</span>
                    <span>Usa buena iluminación natural</span>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-green-600">•</span>
                    <span>Enfoca bien el producto</span>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-green-600">•</span>
                    <span>Muestra el producto completo</span>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-green-600">•</span>
                    <span>Fondo limpio y sin distracciones</span>
                </div>
            </div>
        </div>

        <!-- Tip sobre la descripción -->
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4 sm:p-6">
            <h3 class="text-base font-bold text-blue-800 mb-2 flex items-center">
                <span class="mr-2">✏️</span> Sobre la descripción
            </h3>
            <p class="text-sm sm:text-base text-blue-700">
                Hemos incluido una descripción predeterminada que funciona para cualquier producto. 
                Puedes personalizarla completamente o dejarla como está. ¡Una buena descripción ayuda a vender más!
            </p>
        </div>
    </div>
</div>

<script>
    let stream = null;
    let video = null;
    let canvas = null;
    let context = null;

    // Descripción predeterminada
    const defaultDescription = '🌱 Cultivado con dedicación para brindarte frescura y sabor excepcional';

    function resetDescription() {
        const descripcionField = document.getElementById('descripcion');
        descripcionField.value = defaultDescription;
        descripcionField.focus();
    }

    function openCamera() {
        video = document.getElementById('video');
        canvas = document.getElementById('canvas');
        context = canvas.getContext('2d');
        
        const cameraContainer = document.getElementById('cameraContainer');
        const captureBtn = document.getElementById('captureBtn');
        const closeBtn = document.getElementById('closeBtn');
        const imagePreview = document.getElementById('imagePreview');

        // Ocultar preview si existe
        imagePreview.style.display = 'none';

        // Configurar constrains para la cámara (optimizados para móvil)
        const constraints = {
            video: {
                width: { ideal: 640, max: 1280 },
                height: { ideal: 480, max: 720 },
                facingMode: 'environment' // Usar cámara trasera si está disponible
            }
        };

        navigator.mediaDevices.getUserMedia(constraints)
            .then(function(mediaStream) {
                stream = mediaStream;
                video.srcObject = stream;
                
                // Mostrar la cámara y los botones
                cameraContainer.style.display = 'block';
                captureBtn.style.display = 'inline-block';
                closeBtn.style.display = 'inline-block';

                // Asegurar que el video se reproduce
                video.addEventListener('loadedmetadata', function() {
                    video.play();
                });
            })
            .catch(function(err) {
                console.error("Error al acceder a la cámara: ", err);
                alert("No se pudo acceder a la cámara. Puedes usar el botón de galería como alternativa.");
            });
    }

    function capturePhoto() {
        if (!video || !canvas || !context) {
            console.error("Video o canvas no están disponibles");
            return;
        }

        // Ajustar canvas al tamaño del video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Capturar la imagen del video
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Convertir a base64 con calidad optimizada
        const dataURL = canvas.toDataURL('image/jpeg', 0.8);
        
        // Guardar en el campo oculto
        document.getElementById('imagen_base64').value = dataURL;
        
        // Mostrar preview
        const capturedImage = document.getElementById('capturedImage');
        const imagePreview = document.getElementById('imagePreview');
        capturedImage.src = dataURL;
        imagePreview.style.display = 'block';
        
        // Cerrar la cámara
        closeCamera();
    }

    function closeCamera() {
        if (stream) {
            stream.getTracks().forEach(track => {
                track.stop();
            });
            stream = null;
        }
        
        const cameraContainer = document.getElementById('cameraContainer');
        const captureBtn = document.getElementById('captureBtn');
        const closeBtn = document.getElementById('closeBtn');
        
        cameraContainer.style.display = 'none';
        captureBtn.style.display = 'none';
        closeBtn.style.display = 'none';
        
        if (video) {
            video.srcObject = null;
        }
    }

    function clearImage() {
        document.getElementById('imagen_base64').value = '';
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('imagen').value = '';
    }

    // Manejar vista previa de archivo seleccionado
    document.getElementById('imagen').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const capturedImage = document.getElementById('capturedImage');
                const imagePreview = document.getElementById('imagePreview');
                capturedImage.src = e.target.result;
                imagePreview.style.display = 'block';
                // Limpiar base64 si se selecciona archivo
                document.getElementById('imagen_base64').value = '';
            };
            reader.readAsDataURL(file);
        }
    });

    // Manejar el envío del formulario
    document.getElementById('productoForm').addEventListener('submit', function(event) {
        const imageBase64 = document.getElementById('imagen_base64').value;
        const fileInput = document.getElementById('imagen');
        
        if (imageBase64 && !fileInput.files.length) {
            event.preventDefault();
            
            // Convertir base64 a Blob
            const blob = dataURItoBlob(imageBase64);
            const file = new File([blob], "producto_camara.jpg", { type: 'image/jpeg' });
            
            // Crear un DataTransfer para asignar el archivo
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
            
            // Enviar el formulario
            setTimeout(() => {
                this.submit();
            }, 100);
        }
    });

    // Función para convertir dataURI a Blob
    function dataURItoBlob(dataURI) {
        const byteString = atob(dataURI.split(',')[1]);
        const mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
        const ab = new ArrayBuffer(byteString.length);
        const ia = new Uint8Array(ab);
        
        for (let i = 0; i < byteString.length; i++) {
            ia[i] = byteString.charCodeAt(i);
        }
        
        return new Blob([ab], { type: mimeString });
    }

    // Limpiar recursos al salir de la página
    window.addEventListener('beforeunload', function() {
        closeCamera();
    });

    // Inicializar con descripción predeterminada si el campo está vacío al cargar
    document.addEventListener('DOMContentLoaded', function() {
        const descripcionField = document.getElementById('descripcion');
        if (!descripcionField.value.trim() || descripcionField.value === descripcionField.placeholder) {
            descripcionField.value = defaultDescription;
        }
    });
</script>

@endsection