<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Sistema de Reporte de Incendios
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <h1 class="text-2xl font-bold mb-4">Bienvenido</h1>

                <p class="mb-6 text-gray-700">
                    Este sistema permite registrar reportes de incendios, visualizar alertas y gestionar notificaciones.
                </p>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('login') }}"
                       class="inline-block px-4 py-2 bg-gray-800 text-white rounded text-center">
                        Iniciar sesión
                    </a>

                    <a href="{{ route('register') }}"
                       class="inline-block px-4 py-2 bg-red-600 text-white rounded text-center">
                        Registrarse
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>