<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nuevo reporte de incendio
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!--si se envio el mensaje sale verde-->
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                <!--fin-->
                <!--si hubo algun error sale en rojo-->
                <!--
                revisa si hay al menos un error
                se muestra el mensaje de revisa los campos 
                -->
                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                        Revisa los campos del formulario.
                    </div>
                @endif
                <!--fin-->
                <!--abre el formulario (envia los datos ingresados
                a la ruta de reposts.store
                -->
                <form method="POST" action="{{ route('reports.store') }}">
                    @csrf
                    <!--fin-->
                    <div class="mb-4">
                        <label class="block mb-2 font-medium">Descripción</label>
                        <!--el old(description) lo que hace si el formulario
                        fallo la validacion y se recargo recupera el texto que el 
                        usuario habia escrito para no perderlo-->
                        <textarea name="description" class="w-full border rounded p-2" rows="4">{{ old('description') }}</textarea>
                        <!--fin-->
                        <!--si se detecto un error especifico en el campo de descripcion
                        muestra el mensaje en rojo dentro del campo-->
                        @error('description')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <!--fin-->
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
                            <!--la opcion manual, el select mantiene esta 
                            opcion seleccionada si era la elegida cuando el 
                            formualrio fallo-->
                            <option value="manual" {{ old('location_type') == 'manual' ? 'selected' : '' }}>Manual</option>
                            <!--fin-->
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
                    </div>
                    <div class="mb-4">
                        <label class="block mb-2 font-medium">Selecciona la ubicación en el mapa</label>
                        <p class="text-sm text-gray-500 mb-2">Haz clic en el mapa para marcar el punto exacto del incendio.</p>
                        <!--el leaflet dibuja aca el mapa utilizando el id=mapa-->
                        <div id="mapa" style="height: 350px; border-radius: 8px; border: 1px solid #ccc;"></div>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">
                        Enviar reporte
                    </button>
                </form>
            </div>
        </div>
    </div>

 {{-- Leaflet CSS y JS (mapa gratuito sin API key) --}}
 <!--carga los estilos visuales de leaflet-->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!--fin-->
<!--libreria de leaflet-->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!--fin-->

<script>
    const inputLat  = document.getElementById('latitude');
    const inputLng  = document.getElementById('longitude');
    const inputTipo = document.getElementById('location_type');

    // Centro aproximado de Arequipa
    // en este caso es el punto inicial donde se habre el mapa 
    const defaultLat = -16.409047;
    const defaultLng = -71.537451;

    // Inicializar mapa
    //aca se crea el mapa , donde el 13 es en el nivel de zoom
    const mapa = L.map('mapa').setView([defaultLat, defaultLng], 13);

    //configura las imagenes del mapa, usando los servidores de openstreet x, y ,z
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        //son los cretidos que aparece a la ezquina del mapa
        attribution: '© OpenStreetMap contributors'
    //agrega las imagenes del mapa creado     
    }).addTo(mapa);

    // Marcador del mapa , variable que guardara, es null por que 
    // aun no existe
    let marcador = null;

    // Si ya hay valores guardados (por old()), colocando el marcador
    if (inputLat.value && inputLng.value) {
        //convierte los textos de los campos a numeros decimales
        //para que leaftle los entineda XD
        const lat = parseFloat(inputLat.value);
        const lng = parseFloat(inputLng.value);
        //crea un marcador en las coordenadas elegidas y lo agrega al mapa
        marcador = L.marker([lat, lng]).addTo(mapa);
        //en este caso centra el mapa con zoom 15
        mapa.setView([lat, lng], 15);
    }

    // Clic en el mapa → guardar coordenadas
    mapa.on('click', (e) => {
        //aca extrae latitud y longitud exacta del punto del click
        //usando destructuring
        const { lat, lng } = e.latlng;

        //escribe la latitud en el campo formulario con 7 decimales
        inputLat.value  = lat.toFixed(7);
        inputLng.value  = lng.toFixed(7);
        //cambia a manual , por que el usuario eligio la ubicacion a mano
        inputTipo.value = 'manual';
        
        //aca es si ya existe un marcador , entonces lo mueve al punto
        //seleccionado , si no existe lo crea
        if (marcador) {
            marcador.setLatLng([lat, lng]);
        } else {
            marcador = L.marker([lat, lng]).addTo(mapa);
        }

        //aca se pone un globito al marcador con las coordenadas  a 5 decimales
        //y lo abre automaticamente jajajaj
        marcador.bindPopup(`📍 ${lat.toFixed(5)}, ${lng.toFixed(5)}`).openPopup();
    });

    // Botón ubicación actual → centra y marca en el mapa
    document.getElementById('btnUbicacionActual').addEventListener('click', () => {
        //esto es cuando el navegador no tiene GPS, mostrando una alerta
        if (!navigator.geolocation) {
            alert('Tu navegador no permite geolocalización.');
            return;
        }

        //solicita al navegador la posicion actual del dispositivo
        navigator.geolocation.getCurrentPosition(
            //si es que respondio ejecuta esto
            (position) => {
                //extrae del gps la la y long
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                inputLat.value  = lat.toFixed(7);
                inputLng.value  = lng.toFixed(7);
                //guarda las coordenadas
                inputTipo.value = 'auto';

                mapa.setView([lat, lng], 16);

                if (marcador) {
                    marcador.setLatLng([lat, lng]);
                } else {
                    marcador = L.marker([lat, lng]).addTo(mapa);
                }

                marcador.bindPopup('📍 Tu ubicación actual').openPopup();
            },
            //aca dice que cuando el gps fallo o el usuario lo denego
            //muestra la alerta 
            () => {
                alert('No se pudo obtener la ubicación. Haz clic en el mapa para marcarla.');
            },
            //tres opciones del gps, maxima precision posible
            //espera maximo 10 segundos 
            //no usa ubicaciones guardadas en el cache "maximumAge:0"
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });
</script>
</x-app-layout>