@extends('layouts.app2')

@section('content')
<div class="container mx-auto mt-12 max-w-5xl px-4 text-center">
    <!-- Sección de mensaje -->
    <div class="bg-gradient-to-r from-gray-100 to-gray-50 border border-gray-200 shadow-xl rounded-3xl p-8 sm:p-12">
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-800 mb-4 sm:mb-6">
            🚧 ¡Estamos trabajando en nuevas funcionalidades!
        </h1>
        <p class="text-gray-600 text-base sm:text-lg leading-relaxed mb-6 sm:mb-8">
            Nuestro equipo está desarrollando nuevas herramientas y funcionalidades diseñadas especialmente para mejorar tu experiencia. 
            ¡Pronto estarán disponibles! Agradecemos tu paciencia y confianza en nuestra plataforma. 
        </p>
        <div class="flex justify-center">
            <img src="{{ asset('images/progre.svg') }}" alt="Trabajando en nuevas funcionalidades" 
                class="w-full max-w-xs sm:max-w-md lg:max-w-lg rounded-2xl shadow-lg transform hover:scale-105 transition duration-300 ease-in-out">
        </div>
    </div>
</div>
@endsection
