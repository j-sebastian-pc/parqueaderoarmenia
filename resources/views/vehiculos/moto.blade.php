@extends('layouts.app')

@section('title', 'Motos')

@section('content')
    @include('partials.nav-menu')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Motos</h3>

        <!-- Mensajes de éxito o error -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="mt-4 text-center d-flex justify-content-center gap-4">
            <h5>Motos en Parqueo: <span class="badge bg-primary">{{ $motos->where('estado', 'activo')->count() }}</span></h5>
            <h5>Motos Salidas: <span class="badge bg-success">{{ $motos->where('estado', '!=', 'activo')->count() }}</span></h5>
        </div>

        <!-- Añadir buscador por placa -->
        <div class="row justify-content-center mb-4 mt-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="buscadorPlaca" class="form-control" placeholder="Buscar por placa..." autocomplete="off">
                </div>
            </div>
        </div>

        <div class="text-center my-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistro">
                Registrar Entrada de Moto
            </button>
        </div>

        <!-- Modal para registrar moto -->
        <div class="modal fade" id="modalRegistro" tabindex="-1" aria-labelledby="modalRegistroLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalRegistroLabel">Registrar Entrada de Moto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('motos.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="placaMoto" class="form-label">Placa de la Moto</label>
                                <input type="text" name="placaMoto" id="placaMoto" class="form-control" required>
                                <small class="text-muted">Formato: ABC12D</small>
                            </div>
                            <div class="mb-3">
                                <label for="tarifaSeleccionada" class="form-label">Seleccione Tarifa</label>
                                <select name="tarifa_tipo" id="tarifaSeleccionada" class="form-control" required>
                                    <option value="" disabled selected>Seleccione una opción</option>
                                    <option value="hora">Hora ($1,000)</option>
                                    <option value="12_horas">12 Horas ($8,000)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="tarifa_valor" class="form-label">Precio a pagar ($)</label>
                                <input type="number" name="tarifa_valor" id="tarifa_valor" class="form-control" readonly>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Registrar Entrada</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de motos -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Placa</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Horas</th>
                            <th>Tarifa ($)</th>
                            <th>Total a Pagar ($)</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($motos as $moto)
                            <tr>
                                <td>{{ $moto->placaMoto }}</td>
                                <td>{{ \Carbon\Carbon::parse($moto->entradaMoto)->format('d/m/Y H:i') }}</td>
                                <td>
                                    {{ $moto->salidaMoto ? \Carbon\Carbon::parse($moto->salidaMoto)->format('d/m/Y H:i') : 'Aún en parqueadero' }}
                                </td>
                                <td>
                                    {{ $moto->hora ?? '---' }}
                                </td>
                                <td>{{ number_format($moto->tarifa_valor, 0) }}</td>
                                <td>
                                    {{ $moto->total_cobro ? number_format($moto->total_cobro, 0) : '---' }}
                                </td>
                                <td>
                                    <span class="badge {{ $moto->estado == 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($moto->estado ?? 'Desconocido') }}
                                    </span>
                                </td>
                                <td>
                                    @if ($moto->estado == 'activo')
                                        <form action="{{ route('motos.salida', $moto->idMoto) }}" method="POST"
                                            onsubmit="return confirm('¿Está seguro de registrar la salida de esta moto?');">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Registrar Salida</button>
                                        </form>
                                    @else
                                        <span class="text-success fw-bold">Salió</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No hay motos registradas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Script para actualizar el precio al seleccionar una tarifa
        document.addEventListener('DOMContentLoaded', function() {
            const tarifaSelect = document.getElementById('tarifaSeleccionada');
            const precioInput = document.getElementById('tarifa_valor');

            tarifaSelect.addEventListener('change', function() {
                if (this.value === 'hora') {
                    precioInput.value = 1000;
                } else if (this.value === '12_horas') {
                    precioInput.value = 8000;
                } else {
                    precioInput.value = '';
                }
            });
            
            // Buscador automático para filtrar por placa
            const buscador = document.getElementById('buscadorPlaca');
            const tabla = document.querySelector('table');
            const filas = tabla.querySelectorAll('tbody tr');
            
            buscador.addEventListener('input', function() {
                const textoBusqueda = this.value.trim().toUpperCase();
                let resultadosEncontrados = 0;
                
                filas.forEach(function(fila) {
                    const celda = fila.querySelector('td:first-child'); // La primera celda contiene la placa
                    if (!celda) return;
                    
                    const placa = celda.textContent.trim().toUpperCase();
                    
                    if (placa.includes(textoBusqueda)) {
                        fila.style.display = '';
                        resultadosEncontrados++;
                    } else {
                        fila.style.display = 'none';
                    }
                });
                
                // Mostrar mensaje si no hay resultados
                let mensajeNoResultados = document.getElementById('sin-resultados');
                
                if (resultadosEncontrados === 0 && textoBusqueda !== '') {
                    if (!mensajeNoResultados) {
                        mensajeNoResultados = document.createElement('tr');
                        mensajeNoResultados.id = 'sin-resultados';
                        mensajeNoResultados.innerHTML = `<td colspan="8" class="text-center">No se encontraron motos con la placa "${textoBusqueda}"</td>`;
                        tabla.querySelector('tbody').appendChild(mensajeNoResultados);
                    } else {
                        mensajeNoResultados.querySelector('td').textContent = `No se encontraron motos con la placa "${textoBusqueda}"`;
                        mensajeNoResultados.style.display = '';
                    }
                } else if (mensajeNoResultados) {
                    mensajeNoResultados.style.display = 'none';
                }
            });
        });
    </script>
    @include('Footer.index')
@endsection