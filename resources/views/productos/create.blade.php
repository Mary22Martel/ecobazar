@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-12 max-w-3xl">
    <h1 class="text-5xl font-extrabold text-center text-green-500 mb-10">Añadir Nuevo Producto</h1>

    <!-- Mostrar mensajes de error si hay validación fallida -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">¡Error!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario para añadir un nuevo producto -->
    <form id="productoForm" action="{{ route('productos.store') }}" method="POST" class="bg-white shadow-xl rounded-lg p-10 space-y-8" enctype="multipart/form-data">
        @csrf

        <!-- Nombre del Producto -->
        <div>
            <label for="nombre" class="block text-lg font-semibold text-gray-700 mb-2">Nombre del Producto</label>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre del Producto" value="{{ old('nombre') }}" class="block w-full p-4 border border-gray-300 rounded-lg focus:ring-green-400 focus:border-green-400" required>
        </div>

        <!-- Unidad de medida -->
        <div>
            <label for="medida" class="block text-lg font-semibold text-gray-700 mb-2">Unidad de medida</label>
            <select name="medida_id" id="medida" class="block w-full p-4 border border-gray-300 rounded-lg focus:ring-green-400 focus:border-green-400" required>
                @foreach ($medidas as $medida)
                    <option value="{{ $medida->id }}">{{ $medida->nombre }}</option>
                @endforeach
            </select>
        </div>

        <!-- Precio y Cantidad (Grid en una sola fila) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Precio -->
            <div>
                <label for="precio" class="block text-lg font-semibold text-gray-700 mb-2">Precio</label>
                <input type="number" name="precio" id="precio" placeholder="Ej. 25.99" value="{{ old('precio') }}" step="0.01" class="block w-full p-4 border border-gray-300 rounded-lg focus:ring-green-400 focus:border-green-400" required>
            </div>

            <!-- Cantidad Disponible -->
            <div>
                <label for="cantidad_disponible" class="block text-lg font-semibold text-gray-700 mb-2">Cantidad Disponible</label>
                <input type="number" name="cantidad_disponible" id="cantidad_disponible" placeholder="Ej. 100" value="{{ old('cantidad_disponible') }}" class="block w-full p-4 border border-gray-300 rounded-lg focus:ring-green-400 focus:border-green-400" required>
            </div>
        </div>

        <!-- Categoría -->
        <div>
            <label for="categoria" class="block text-lg font-semibold text-gray-700 mb-2">Categoría</label>
            <select name="categoria_id" id="categoria" class="block w-full p-4 border border-gray-300 rounded-lg focus:ring-green-400 focus:border-green-400" required>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                @endforeach
            </select>
        </div>

        <!-- Descripción -->
        <div>
            <label for="descripcion" class="block text-lg font-semibold text-gray-700 mb-2">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="4" placeholder="Descripción detallada del producto..." class="block w-full p-4 border border-gray-300 rounded-lg focus:ring-green-400 focus:border-green-400" required>{{ old('descripcion') }}</textarea>
        </div>

        <!-- Imagen del Producto con acceso a cámara o archivo -->
        <div>
            <label for="imagen" class="block text-lg font-semibold text-gray-700 mb-2">Imagen del Producto (JPG, PNG)</label>
            <div class="flex space-x-4">
                <button type="button" onclick="openCamera()" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Abrir Cámara</button>
                <input type="file" name="imagen" id="imagen" accept="image/*" class="block w-full p-4 border border-gray-300 rounded-lg focus:ring-green-400 focus:border-green-400">
            </div>
            <!-- Video para cámara y canvas para tomar la imagen -->
            <video id="video" width="320" height="240" autoplay style="display:none;"></video>
            <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
            <input type="hidden" id="imagen_base64" name="imagen_base64"> <!-- Campo oculto para almacenar la imagen base64 -->
        </div>

        <!-- Botón de Guardar -->
        <div class="pt-6">
            <button type="submit" id="guardarProducto" class="w-full py-4 bg-green-500 text-white text-lg font-bold rounded-lg hover:bg-green-600 transition duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-green-300 focus:ring-opacity-50">
                Guardar Producto
            </button>
        </div>
    </form>
</div>

<script>
    function openCamera() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const imageInput = document.getElementById('imagen_base64');

        // Mostrar el video para usar la cámara
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(stream) {
                video.style.display = 'block';
                video.srcObject = stream;

                // Añadir evento para capturar una foto cuando el usuario lo desee
                video.addEventListener('click', function() {
                    // Dibujar la imagen del video en el canvas
                    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

                    // Convertir la imagen del canvas a base64
                    const dataURL = canvas.toDataURL('image/jpeg');

                    // Mostrar el canvas
                    canvas.style.display = 'block';

                    // Asignar el valor de la imagen base64 al campo oculto
                    imageInput.value = dataURL;

                    // Detener la cámara después de capturar la imagen
                    const stream = video.srcObject;
                    const tracks = stream.getTracks();

                    tracks.forEach(function(track) {
                        track.stop();
                    });

                    video.style.display = 'none';
                });
            })
            .catch(function(err) {
                console.log("Error al acceder a la cámara: " + err);
            });
    }

    document.getElementById('productoForm').addEventListener('submit', function(event) {
        const imageBase64 = document.getElementById('imagen_base64').value;
        if (imageBase64) {
            event.preventDefault(); // Prevenir el envío del formulario

            // Convertir base64 a Blob
            const blob = dataURItoBlob(imageBase64);
            const fileInput = document.getElementById('imagen');
            const file = new File([blob], "captura.jpg", { type: 'image/jpeg' });

            // Asignar el archivo al input de tipo file
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;

            // Reanudar el envío del formulario
            this.submit();
        }
    });

    // Función para convertir una cadena base64 a un Blob
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
</script>

@endsection