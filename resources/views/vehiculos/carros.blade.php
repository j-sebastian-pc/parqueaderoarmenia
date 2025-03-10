@extends('layouts.app')

@section('title', 'Carros')

@section('content')
    @include('partials.nav-menu')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Carros</h3>

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
            <h5>Carros en Parqueo: <span class="badge bg-primary">{{ $carrosEnParqueo }}</span></h5>
            <h5>Carros Salidos: <span class="badge bg-success">{{ $carrosSalidos }}</span></h5>
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
                Registrar Entrada de Carro
            </button>
        </div>

        <!-- Modal para registrar carro -->
        <div class="modal fade" id="modalRegistro" tabindex="-1" aria-labelledby="modalRegistroLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalRegistroLabel">Registrar Entrada de Carro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('carros.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="placaCarro" class="form-label">Placa del Carro</label>
                                <input type="text" name="placaCarro" id="placaCarro" class="form-control" required>
                                <small class="text-muted">Formato: ABC123</small>
                            </div>
                            <div class="mb-3">
                                <label for="tarifaSeleccionada" class="form-label">Seleccione Tarifa</label>
                                <select name="tarifaSeleccionada" id="tarifaSeleccionada" class="form-control" required>
                                    <option value="" disabled selected>Seleccione una opción</option>
                                    <option value="hora">Hora ($1,500)</option>
                                    <option value="12_horas">12 Horas ($12,000)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio a pagar ($)</label>
                                <input type="text" id="precio" class="form-control" readonly>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Registrar Entrada</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de carros -->
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
                        @forelse($carros as $carro)
                            <tr>
                                <td>{{ $carro->placaCarro }}</td>
                                <td>{{ \Carbon\Carbon::parse($carro->entradaCarro)->format('d/m/Y H:i') }}</td>
                                <td>
                                    {{ $carro->salidaCarro ? \Carbon\Carbon::parse($carro->salidaCarro)->format('d/m/Y H:i') : 'Aún en parqueadero' }}
                                </td>
                                <td>
                                    {{ $carro->hora ?? '---' }}
                                </td>
                                <td>{{ number_format($carro->tarifa_valor, 0) }}</td>
                                <td>
                                    {{ $carro->total_cobro ? number_format($carro->total_cobro, 0) : '---' }}
                                </td>
                                <td>
                                    <span class="badge {{ $carro->estado == 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($carro->estado ?? 'Desconocido') }}
                                    </span>
                                </td>
                                <td>
                                    @if ($carro->estado == 'activo')
                                        <form action="{{ route('carros.salida', $carro->idCarro) }}" method="POST"
                                            onsubmit="return confirm('¿Está seguro de registrar la salida de este vehículo?');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-danger btn-sm">Registrar Salida</button>
                                        </form>
                                    @else
                                        <span class="text-success fw-bold">Salió</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No hay carros registrados</td>
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
            const precioInput = document.getElementById('precio');

            tarifaSelect.addEventListener('change', function() {
                if (this.value === 'hora') {
                    precioInput.value = '1,500';
                } else if (this.value === '12_horas') {
                    precioInput.value = '12,000';
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
                        mensajeNoResultados.innerHTML = `<td colspan="8" class="text-center">No se encontraron vehículos con la placa "${textoBusqueda}"</td>`;
                        tabla.querySelector('tbody').appendChild(mensajeNoResultados);
                    } else {
                        mensajeNoResultados.querySelector('td').textContent = `No se encontraron vehículos con la placa "${textoBusqueda}"`;
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