@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @include('partials.nav-menu')
    
    <div class="container mt-5">
        <h3 class="text-center mb-4">Bienvenido al Dashboard</h3>

        <!-- Mensajes de éxito o error -->
        @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Contenido principal -->
        <div class="card">
            <div class="card-body">
                <div class="text-center py-5">
                    <h4>Sistema de Gestión de Parqueadero</h4>
                    <p class="text-muted">
                        Seleccione una opción del menú superior para comenzar.
                    </p>
                </div>
            </div>
        </div>
    </div>
    @include('Footer.index')
@endsection