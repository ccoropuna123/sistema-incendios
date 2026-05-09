<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    // Muestra el formulario para crear un reporte
    public function create()
    {
        return view('reports.create');
    }

    // Guarda el reporte en la base de datos
    public function store(Request $request)
    {
        // Validación básica
        $validated = $request->validate([
            'description' => 'nullable|string|max:1000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_type' => 'required|in:auto,manual',
        ]);

        // Crear el reporte
        Report::create([
            'user_id' => Auth::id(),
            'description' => $validated['description'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'location_type' => $validated['location_type'],
            'status' => 'enviado',
        ]);

        return redirect()
            ->route('reports.create')
            ->with('success', 'Reporte enviado correctamente.');
    }
}