<?php

namespace App\Http\Controllers;

// "Importamos" herramientas que vamos a usar más abajo
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Definimos la clase ReportController
class ReportController extends Controller
{
    // Muestra el formulario para crear un reporte
    public function create()
    {
        // 'reports.create' significa: busca el archivo
        return view('reports.create');
    }

    // Guarda el reporte en la base de datos
    public function store(Request $request)
    {
        // Validación básica
        $validated = $request->validate([
            //  nullable: opcional y required es obligatorio
            'description' => 'nullable|string|max:1000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_type' => 'required|in:auto,manual',
        ]);

        // Crear el reporte
        // Report::create() inserta una nueva fila en la tabla 'reports'
        Report::create([
            'user_id' => Auth::id(),
            'description' => $validated['description'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'location_type' => $validated['location_type'],
            'status' => 'enviado',
        ]);
        // Después de guardar, lo mandamos de vuelta al formulario
        // y le mostramos un mensaje de éxito
        return redirect()
            ->route('reports.create')
            ->with('success', 'Reporte enviado correctamente.');
    }
}