<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bicicleta;

class BicicletaController extends Controller
{
    public function index()
    {
        $bicicletas = Bicicleta::all();
        return view('bicicletas.index', compact('bicicletas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'color' => 'required|string|max:50',
            'estado' => 'required|in:disponible,ocupada'
        ]);

        Bicicleta::create($request->all());

        return redirect()->route('bicicletas.index')->with('success', 'Bicicleta registrada correctamente');
    }

    public function registrarSalida($id)
    {
        $bicicleta = Bicicleta::findOrFail($id);
        $bicicleta->estado = 'ocupada';
        $bicicleta->save();

        return redirect()->route('bicicletas.index')->with('success', 'Salida registrada');
    }
}
