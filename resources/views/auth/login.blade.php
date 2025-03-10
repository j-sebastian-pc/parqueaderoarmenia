@extends('layouts.auth')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">

@section('content')
    <div class="login-container">
        <div class="login-box">
            <h1 class="login-title">Iniciar Sesión</h1>

            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="login-form">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-input" required>
                </div>

                <button type="submit" class="login-button">Iniciar Sesión</button>
            </form>

            <div class="register-link">
                <p>¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a></p>
            </div>
        </div>
    </div>
@endsection
