<?php

namespace App\Http\Controllers;

use App\Models\Moto;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MotoController extends Controller
{
    // Constructor para proteger las rutas
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
{
    $motos = Moto::all();
    return view('vehiculos.moto', compact('motos')); 
}
    // funtion para crear una moto
    public function create()
    {
        return view('motos.create');
    }

        // Función para registrar nueva moto
    public function store(Request $request)
    {
        $request->validate([
            'placaMoto' => 'required|string|max:10',
            'tarifa_tipo' => 'required|in:hora,12_horas',
            'tarifa_valor' => 'required|numeric',
        ]);

        $data = $request->all();
        $data['estado'] = 'activo';
        $data['entradaMoto'] = now();

        Moto::create($data);
        return redirect()->route('motos.index')
            ->with('success', 'Moto registrada con éxito.');
    }

        // Función para mostrar información de la moto por id
    public function show($id)
    {
        $moto = Moto::findOrFail($id);
        return view('motos.show', compact('moto'));
    }

        // Función para editar información de la moto por id
    public function edit($id)
    {
        $moto = Moto::findOrFail($id);
        return view('motos.edit', compact('moto'));
    }

    // Función para actualizar información de la moto por id
    public function update(Request $request, $id)
    {
        $request->validate([
            'placaMoto' => 'required|string|max:10',
            'tarifa_tipo' => 'required|in:hora,12_horas',
            'tarifa_valor' => 'required|numeric',
        ]);

        $moto = Moto::findOrFail($id);
        $moto->update($request->all());

        return redirect()->route('motos.index')
            ->with('success', 'Información de moto actualizada correctamente');
    }

    // Función para eliminar moto por id    
    public function destroy($id)
    {
        $moto = Moto::findOrFail($id);
        $moto->delete();

        return redirect()->route('motos.index')
            ->with('success', 'Moto eliminada correctamente');
    }

    // Función para registrar salida de moto por id
    public function registrarSalida($id)
{
    try {
        $moto = Moto::findOrFail($id);
        
        if ($moto->estado !== 'activo' || $moto->salidaMoto) {
            return redirect()->route('motos.index')
                ->with('error', 'Esta moto ya ha salido.');
        }
        
        // Usar Carbon para cálculos de tiempo y fechas
        $ahora = now();
        $entrada = \Carbon\Carbon::parse($moto->entradaMoto);
        
        // Calcular horas y redondear a un numero entero
        $minutosTotal = $entrada->diffInMinutes($ahora);
        $horas = ceil($minutosTotal / 60);
        
        // Calcular total a cobrar
        $totalCobro = 0;
        
        if ($moto->tarifa_tipo === 'hora') {
            $totalCobro = $horas * $moto->tarifa_valor;
        } else if ($moto->tarifa_tipo === '12_horas') {
            // Si es tarifa de 12 horas
            if ($horas <= 12) {
                $totalCobro = $moto->tarifa_valor;
            } else {
                // Si pasó de 12 horas, cobrar tarifa adicional
                $horasAdicionales = $horas - 12;
                $tarifaHora = 1000; // 
                $totalCobro = $moto->tarifa_valor + ($horasAdicionales * $tarifaHora);
            }
        }
        
        $moto->salidaMoto = $ahora;
        $moto->hora = $horas;
        $moto->total_cobro = $totalCobro;
        $moto->estado = 'finalizado';
        $moto->save();
        
        return redirect()->route('motos.index')
            ->with('success', 'Salida de moto registrada correctamente. Total a pagar: $' . number_format($totalCobro, 0));
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Error al registrar salida: ' . $e->getMessage());
        return redirect()->route('motos.index')
            ->with('error', 'Error al registrar la salida: ' . $e->getMessage());
    }
}
    // Función para mostrar motos activas
    public function motosActivas()
    {
        $motos = Moto::where('estado', 'activo')->get();
        return view('motos.activas', compact('motos'));
    }
}