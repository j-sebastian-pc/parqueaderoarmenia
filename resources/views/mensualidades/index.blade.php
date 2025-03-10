@extends('layouts.app')

@section('title', 'Mensualidades')

@section('content')
    @include('partials.nav-menu')
    <div class="container mt-5">
        <h3 class="text-center mb-4">Mensualidades</h3>

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

        <!-- Añadir buscador por nombre o placa -->
        <div class="row justify-content-center mb-4 mt-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="buscadorMensualidad" class="form-control" placeholder="Buscar por nombre o placa..." autocomplete="off">
                </div>
            </div>
        </div>

        <!-- Botón para agregar -->
        <div class="text-center my-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaMensualidad">
                Agregar Mensualidad
            </button>
        </div>

        <!-- Tabla de mensualidades -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Placa</th>
                            <th>Teléfono</th>
                            <th>Entrada</th>
                            <th>Días Restantes</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($mensualidades) && $mensualidades->count() > 0)
                            @foreach ($mensualidades as $mensualidad)
                                <tr data-id="{{ $mensualidad->idMensualidad }}">
                                    <td>{{ $mensualidad->nombreMensualidad ?? 'N/A' }}</td>
                                    <td>{{ $mensualidad->placaMensualidad ?? 'N/A' }}</td>
                                    <td>{{ $mensualidad->telefonoMensualidad ?? 'N/A' }}</td>
                                    <td>{{ $mensualidad->entradaMensualidad ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $diasRestantes = 0;
                                            if (isset($mensualidad->entradaMensualidad)) {
                                                $entradaTimestamp = strtotime($mensualidad->entradaMensualidad);
                                                $diferenciaEnDias = floor((time() - $entradaTimestamp) / (60 * 60 * 24));
                                                $diasRestantes = 30 - $diferenciaEnDias;
                                                if ($diasRestantes < 0) {
                                                    $diasRestantes = 0;
                                                }
                                            }
                                        @endphp
                                        {{ $diasRestantes }}
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success btn-renovar" data-id="{{ $mensualidad->idMensualidad }}">
                                                Renovar
                                            </button>
                                            <button type="button" class="btn btn-danger btn-eliminar" data-id="{{ $mensualidad->idMensualidad }}">
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center text-muted">No hay mensualidades registradas</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para agregar nueva mensualidad -->
    <div class="modal fade" id="modalNuevaMensualidad" tabindex="-1" aria-labelledby="modalNuevaMensualidadLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevaMensualidadLabel">Agregar Mensualidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('mensualidades.store') }}" method="POST">
                        @csrf
                        <!-- Campos ocultos para validación -->
                        <input type="hidden" name="idUsuario" value="{{ auth()->id() }}">
                        <input type="hidden" name="tipo_vehiculo" value="carro">
                        <input type="hidden" name="fecha_inicio" value="{{ date('Y-m-d') }}">
                        <input type="hidden" name="fecha_fin" value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                        <input type="hidden" name="valor" value="0">
                        
                        <div class="mb-3">
                            <label for="nombreMensualidad" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombreMensualidad" name="nombreMensualidad"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="placaMensualidad" class="form-label">Placa</label>
                            <input type="text" class="form-control" id="placaMensualidad" name="placaMensualidad"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="telefonoMensualidad" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefonoMensualidad"
                                name="telefonoMensualidad" required>
                        </div>
                        <div class="mb-3">
                            <label for="entradaMensualidad" class="form-label">Entrada</label>
                            <input type="date" class="form-control" id="entradaMensualidad" name="entradaMensualidad"
                                required value="{{ date('Y-m-d') }}">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar que el token CSRF existe
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (!metaToken) {
                console.error('CSRF token no encontrado');
                return;
            }
            const csrfToken = metaToken.content;
            
            // Asegurarse de que el modal funcione correctamente
            const bsModal = new bootstrap.Modal(document.getElementById('modalNuevaMensualidad'));
            
            // Manejador adicional para el botón de agregar (opcional)
            document.querySelector('[data-bs-target="#modalNuevaMensualidad"]').addEventListener('click', function() {
                bsModal.show();
            });
            
            // Manejador para botones de eliminar
            document.querySelectorAll('.btn-eliminar').forEach(button => {
                button.addEventListener('click', async function(e) {
                    e.preventDefault();
                    
                    if(confirm('¿Está seguro que desea eliminar esta mensualidad?')) {
                        const id = this.getAttribute('data-id');
                        
                        try {
                            const response = await fetch(`/mensualidades/eliminar/${id}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            });
                            
                            if (!response.ok) {
                                throw new Error('Error en la respuesta del servidor');
                            }
                            
                            const data = await response.json();
                            alert(data.message);
                            window.location.reload();
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Error al eliminar la mensualidad');
                        }
                    }
                });
            });
            
            // Manejador para botones de renovar
            document.querySelectorAll('.btn-renovar').forEach(button => {
                button.addEventListener('click', async function(e) {
                    e.preventDefault();
                    
                    const id = this.getAttribute('data-id');
                    
                    try {
                        const response = await fetch(`/mensualidades/renovar/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor');
                        }
                        
                        const data = await response.json();
                        alert(data.message);
                        window.location.reload();
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al renovar la mensualidad');
                    }
                });
            });
            
            // Buscador automático para filtrar por nombre o placa
            const buscador = document.getElementById('buscadorMensualidad');
            const tabla = document.querySelector('table');
            const filas = tabla.querySelectorAll('tbody tr');
            
            buscador.addEventListener('input', function() {
                const textoBusqueda = this.value.trim().toUpperCase();
                let resultadosEncontrados = 0;
                
                filas.forEach(function(fila) {
                    const celdaNombre = fila.querySelector('td:nth-child(1)'); // Primera celda (nombre)
                    const celdaPlaca = fila.querySelector('td:nth-child(2)'); // Segunda celda (placa)
                    
                    if (!celdaNombre || !celdaPlaca) return;
                    
                    const nombre = celdaNombre.textContent.trim().toUpperCase();
                    const placa = celdaPlaca.textContent.trim().toUpperCase();
                    
                    if (nombre.includes(textoBusqueda) || placa.includes(textoBusqueda)) {
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
                        mensajeNoResultados.innerHTML = `<td colspan="6" class="text-center">No se encontraron mensualidades con "${textoBusqueda}"</td>`;
                        tabla.querySelector('tbody').appendChild(mensajeNoResultados);
                    } else {
                        mensajeNoResultados.querySelector('td').textContent = `No se encontraron mensualidades con "${textoBusqueda}"`;
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