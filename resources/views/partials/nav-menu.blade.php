<div class="container-fluid bg-dark text-white py-3 mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Parqueadero</h1>
        
        @auth
            <nav class="d-flex">
                <a href="{{ route('carros.index') }}" class="btn btn-outline-light mx-1">Carros</a>
                <a href="{{ route('motos.index') }}" class="btn btn-outline-light mx-1">Motos</a>
                {{-- <a href="{{ route('bicicletas.index') }}" class="btn btn-outline-light mx-1">Bicicletas</a> --}}
                <a href="{{ route('mensualidades.index') }}" class="btn btn-outline-light mx-1">Mensualidades</a>
                
                <!-- Formulario de Cierre de Sesión -->
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger mx-1">Cerrar sesión</button>
                </form>
            </nav>
        @endauth
    </div>
</div>