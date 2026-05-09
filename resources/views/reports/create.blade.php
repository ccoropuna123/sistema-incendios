<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nuevo reporte de incendio
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                        Revisa los campos del formulario.
                    </div>
                @endif

                <form method="POST" action="{{ route('reports.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block mb-2 font-medium">Descripción</label>
                        <textarea name="description" class="w-full border rounded p-2" rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 font-medium">Latitud</label>
                            <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}" class="w-full border rounded p-2">
                            @error('latitude')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-2 font-medium">Longitud</label>
                            <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}" class="w-full border rounded p-2">
                            @error('longitude')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 font-medium">Tipo de ubicación</label>
                        <select name="location_type" id="location_type" class="w-full border rounded p-2">
                            <option value="manual" {{ old('location_type') == 'manual' ? 'selected' : '' }}>Manual</option>
                            <option value="auto" {{ old('location_type') == 'auto' ? 'selected' : '' }}>Automática</option>
                        </select>
                        @error('location_type')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 mb-4">
                        <button type="button" id="btnUbicacionActual"
                                class="px-4 py-2 bg-blue-600 text-white rounded">
                            Usar mi ubicación actual
                        </button>

                        <button type="button" id="btnAbrirMapa"
                                class="px-4 py-2 bg-green-600 text-white rounded">
                            Abrir en Google Maps
                        </button>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">
                        Enviar reporte
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const btnUbicacionActual = document.getElementById('btnUbicacionActual');
        const btnAbrirMapa = document.getElementById('btnAbrirMapa');
        const inputLat = document.getElementById('latitude');
        const inputLng = document.getElementById('longitude');
        const inputTipo = document.getElementById('location_type');

        // Centro aproximado de Arequipa
        const defaultLat = -16.409047;
        const defaultLng = -71.537451;

        btnUbicacionActual.addEventListener('click', () => {
            if (!navigator.geolocation) {
                alert('Tu navegador no permite geolocalización.');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    inputLat.value = position.coords.latitude.toFixed(7);
                    inputLng.value = position.coords.longitude.toFixed(7);
                    inputTipo.value = 'auto';
                    alert('Ubicación actual obtenida correctamente.');
                },
                () => {
                    alert('No se pudo obtener la ubicación. Puedes ingresarla manualmente.');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });

        btnAbrirMapa.addEventListener('click', () => {
            const lat = inputLat.value || defaultLat;
            const lng = inputLng.value || defaultLng;

            const url = `https://www.google.com/maps?q=${lat},${lng}&z=17`;
            window.open(url, '_blank');
        });
    </script>
</x-app-layout>