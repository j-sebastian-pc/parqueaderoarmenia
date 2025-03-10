<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mensualidad;

class MensualidadController extends Controller
{
    public function index()
    {
        // Obtener todas las mensualidades
        $mensualidades = Mensualidad::all();

        // Verificar si se obtuvieron datos
        if ($mensualidades->isEmpty()) {
            // Si no hay datos, pasar un mensaje a la vista
            return view('mensualidades.index', ['mensaje' => 'No hay mensualidades registradas.']);
        }
        return view('mensualidades.index', compact('mensualidades'));
    }

    // Función para registrar nueva mensualidad
    public function store(Request $request)
    {
        // Validar formulario
        $validated = $request->validate([
            'nombreMensualidad' => 'required|string|max:255',
            'placaMensualidad' => 'required|string|max:10',
            'telefonoMensualidad' => 'required|string|max:15',
            'entradaMensualidad' => 'required|date',
            'idUsuario' => 'required|exists:usuarios,idUsuario',
            'tipo_vehiculo' => 'required|in:carro,moto,bicicleta',
            'placa' => 'nullable|string|max:10',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'valor' => 'required|numeric',
        ]);

        // Crear  mensualidad
        Mensualidad::create($validated);

        // Redirigir con un mensaje de éxito
        return redirect()->route('mensualidades.index')->with('success', 'Mensualidad agregada correctamente.');
    }

    // Función para renovar mensualidad
    public function renovarMensualidad($id)
    {
        try {
            $mensualidad = Mensualidad::findOrFail($id);
            $mensualidad->entradaMensualidad = now();
            $mensualidad->save();
    
            return response()->json(['message' => 'Mensualidad renovada exitosamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al renovar la mensualidad: ' . $e->getMessage()], 500);
        }
    }

    // Función para eliminar mensualidad
    public function eliminarMensualidad($id)
    {
        try {
            $mensualidad = Mensualidad::findOrFail($id);
            $mensualidad->delete();
    
            return response()->json(['message' => 'Mensualidad eliminada exitosamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar la mensualidad: ' . $e->getMessage()], 500);
        }
    }
}