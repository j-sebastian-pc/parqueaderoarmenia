{{-- <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parqueadero Armenia - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        body {
            padding-top: 70px;
        }
        .sidebar {
            height: calc(100vh - 70px);
            position: fixed;
            top: 70px;
            left: 0;
            padding: 20px;
            z-index: 100;
            background-color: #f8f9fa;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Parqueadero Armenia</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Registrarse</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->nombre }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    @auth
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 sidebar bg-light border-end">
        <div class="list-group">
            <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
            <a href="{{ route('bicicletas.index') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-bicycle me-2"></i> Bicicletas
            </a>
            <a href="{{ route('carros.index') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-car me-2"></i> Carros
            </a>
            <a href="{{ route('motos.index') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-motorcycle me-2"></i> Motos
            </a>
            <a href="{{ route('carros.activos') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-car-side me-2"></i> Carros Activos
            </a>
            <a href="{{ route('motos.activas') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-motorcycle me-2"></i> Motos Activas
            </a>

            @if(Auth::user()->isAdmin())
                <a href="{{ route('mensualidades.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-calendar-alt me-2"></i> Mensualidades
                </a>
                <a href="{{ route('mensualidades.activas') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-calendar-check me-2"></i> Mensualidades Activas
                </a>
            @endif
        </div>
    </div>
    @endauth

    <!-- Contenido principal -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> --}}
