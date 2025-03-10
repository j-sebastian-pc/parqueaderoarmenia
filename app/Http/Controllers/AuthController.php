<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class AuthController extends Controller
{
    // formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    //  login
    public function login(Request $request)
    {
        // Validar  formulario
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buscar al usuario en la base de datos
        $user = Usuario::where('email', $request->email)->first();

        // Verificar si el usuario existe 
        if (!$user || !Hash::check($request->password, $user->password)) {
            // verifica si el usuario no existe o la contraseña es incorrecta
            return redirect()->route('login')
                ->withErrors(['email' => 'Datos incorrectas.'])
                ->withInput();
        }

        // iniciar sesión si el usuario existe
        Auth::login($user);

        return redirect()->route('dashboard');
    }


    // Cierra la sesión
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Has cerrado sesión.');
    }

    // Muestra el formulario de registro
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // verifica el formulario de registro
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => 'user',
        ]);

        return response()->json(['message' => 'Registro exitoso', 'user' => $user]);
    }

    // Muestra el dashboard solo si está autenticado
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión primero.');
        }

        return view('dashboard.index');
    }
}
