<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CarroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $carros = Carro::orderBy('idCarro', 'DESC')->get();
        
        // Contar carros en parqueo y los que han salido
        $carrosEnParqueo = Carro::whereNull('salidaCarro')->count();
        $carrosSalidos = Carro::whereNotNull('salidaCarro')->count();
        
        return view('vehiculos.carros', compact('carros', 'carrosEnParqueo', 'carrosSalidos'));
    }

    // creacionde del carro
    public function create()
    {
        return view('vehiculos.carros.create');
    }

    public function store(Request $request)
{
    try {
        // Validación de campos requeridos
        $request->validate([
            'placaCarro' => 'required',
            'tarifaSeleccionada' => 'required|in:hora,12_horas'
        ]);
        
        $placa = strtoupper($request->placaCarro);
        
        // Verificar si ya existe un carro con la misma placa sin salida
        $carroExistente = Carro::where('placaCarro', $placa)
                            ->where('estado', 'activo')
                            ->first();
                            
        if ($carroExistente) {
            return redirect()->route('carros.index')
                ->with('error', 'Este carro ya está registrado en el parqueo.');
        }
        
        // Establecer valor de la tarifa seleccionada
        $tarifaValor = $request->tarifaSeleccionada === 'hora' ? 1500 : 12000;
        
        // Crear el registro usando el método  save() 'guardar' 
        $carro = new Carro();
        $carro->placaCarro = $placa;
        $carro->entradaCarro = Carbon::now('America/Bogota');
        $carro->tarifa_tipo = $request->tarifaSeleccionada;
        $carro->tarifa_valor = $tarifaValor;
        $carro->estado = 'activo';
        $carro->save();
        
        return redirect()->route('carros.index')
            ->with('success', 'Carro registrado exitosamente.');
            
    } catch (\Exception $e) {
        Log::error('Error al registrar carro: ' . $e->getMessage());
        return redirect()->route('carros.index')
            ->with('error', 'Error al registrar el carro: ' . $e->getMessage());
    }
}

    // Mostrar la información del carro con el id
    public function show($id)
    {
        $carro = Carro::findOrFail($id);
        return view('vehiculos.carros.show', compact('carro'));
    }

    // Editar la información del carro con el id
    public function edit($id)
    {
        $carro = Carro::findOrFail($id);
        return view('vehiculos.carros.edit', compact('carro'));
    }

    // Actualizar la información del carro con el id
    public function update(Request $request, $id)
    {
        $request->validate([
            'placaCarro' => 'required|string|max:10',
            'tarifa_tipo' => 'required|in:hora,12_horas',
            'tarifa_valor' => 'required|numeric',
        ]);

        $carro = Carro::findOrFail($id);
        $carro->update($request->all());

        return redirect()->route('carros.index')
            ->with('success', 'Información de carro actualizada correctamente');
    }
    // Eliminar el carro con el id
    public function destroy($id)
    {
        $carro = Carro::findOrFail($id);
        $carro->delete();

        return redirect()->route('carros.index')
            ->with('success', 'Carro eliminado correctamente');
    }

    // Registrar la salida del carro con el id
    public function registrarSalida($id)
    {
        try {
            $carro = Carro::findOrFail($id);
            
            if ($carro->estado !== 'activo' || $carro->salidaCarro) {
                return redirect()->route('carros.index')
                    ->with('error', 'Este carro ya ha salido.');
            }
            
            $ahora = Carbon::now('America/Bogota');
            $entrada = Carbon::parse($carro->entradaCarro);
            
            // Calcular horas y redondear para dar un valor entero
            $minutosTotal = $entrada->diffInMinutes($ahora);
            $horas = ceil($minutosTotal / 60);
            
            // Calcular total a cobrar
            $totalCobro = $horas * $carro->tarifa_valor;
            
            $carro->salidaCarro = $ahora;
            $carro->hora = $horas; 
            $carro->total_cobro = $totalCobro;
            $carro->cobro = $totalCobro; 
            $carro->estado = 'inactivo';
            $carro->save();
            
            return redirect()->route('carros.index')
                ->with('success', 'Salida de carro registrada correctamente. Total a pagar: $' . number_format($totalCobro, 0));
        } catch (\Exception $e) {
            Log::error('Error al registrar salida: ' . $e->getMessage());
            return redirect()->route('carros.index')
                ->with('error', 'Error al registrar la salida: ' . $e->getMessage());
        }
    }

    // Buscar carros por placa
    public function buscarPorPlaca(Request $request)
{
    try {
        $placa = strtoupper($request->input('placa'));
        
        if (empty($placa)) {
            return redirect()->route('carros.index')
                ->with('error', 'Debe ingresar una placa para realizar la búsqueda.');
        }
        
        // Buscar carros por placa
        $carros = Carro::where('placaCarro', 'LIKE', '%' . $placa . '%')
                    ->orderBy('idCarro', 'DESC')
                    ->get();
        
        // Contar carros en parqueo y los que han salido (para mantener las estadísticas)
        $carrosEnParqueo = Carro::whereNull('salidaCarro')->count();
        $carrosSalidos = Carro::whereNotNull('salidaCarro')->count();
        
        // Mensaje para mostrar en la vista
        $mensaje = count($carros) > 0 
            ? 'Se encontraron ' . count($carros) . ' resultados para la placa: ' . $placa
            : 'No se encontraron resultados para la placa: ' . $placa;
            
        return view('vehiculos.carros', compact('carros', 'carrosEnParqueo', 'carrosSalidos'))
            ->with('search', true)
            ->with('searchTerm', $placa)
            ->with('message', $mensaje);
            
    } catch (\Exception $e) {
        Log::error('Error al buscar por placa: ' . $e->getMessage());
        return redirect()->route('carros.index')
            ->with('error', 'Error al realizar la búsqueda: ' . $e->getMessage());
    }
}
}