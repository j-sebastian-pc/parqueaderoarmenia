Documentación del Proyecto: Parqueadero Armenia
Introducción
Nombre del Proyecto
"Parqueadero Armenia"

**Descripción del Proyecto**
El proyecto es un sistema de gestión de parqueaderos diseñado para administrar vehículos (carros y motos), registrar entradas y salidas, gestionar usuarios y manejar mensualidades para clientes frecuentes. El sistema cuenta con dos roles principales:

Admin: Acceso completo a la gestión de carros, motos y mensualidades.

User: Acceso limitado a la gestión de carros y motos.

Requisitos del Sistema
Laravel 12

PHP 8.2.12

MySQL

Apache

Composer

**Instrucciones de Instalación**
- Clonar el repositorio.

- Ejecutar composer install para instalar las dependencias.

- Configurar las variables de entorno en el archivo .env.

- Crear una base de datos MySQL.

- Importar la base de datos desde la carpeta database.

- Ejecutar php artisan serve para iniciar el servidor.

**Información de Contacto**

Correo Electrónico: sebascristancho37@gmail.com

**Inicio de Sesión**
Para acceder al sistema:

Dirígete a la página de inicio de sesión.

Ingresa tus credenciales:

Admin:

Correo: admin@example.com

Contraseña: admin123

User:

Correo: user1@example.com

Contraseña: user123

Haz clic en el botón "Iniciar sesión".

**Arquitectura del Sistema**
El sistema sigue una arquitectura MVC (Modelo-Vista-Controlador) con los siguientes componentes:

+-------------------+
|    Usuario        |
+--------+----------+
         |
         v
+--------+----------+
|    Rutas          |
+--------+----------+
         |
         v
+--------+----------+
|   Controladores   |
+--------+----------+
         |
         v
+--------+----------+
|    Modelos        |
+--------+----------+
         |
         v
+--------+----------+
|    Base de Datos  |
+-------------------+

**Base de Datos**
Diagrama de la Base de Datos
El diagrama de la base de datos se encuentra en el archivo diseñode laDB.png.

**Descripción Textual del Diagrama**

usuarios:
Campos: idUsuario, nombre, email, password, rol, created_at, updated_at.
Relación: "tiene" muchas mensualidad.

carros:
Campos: idCarro, placaCarro, entradaCarro, salidaCarro, tarifa_tipo, tarifa_valor, total_cobro, estado, created_at, updated_at, hora, cobro.

motos:
Campos: idMoto, placaMoto, entradaMoto, salidaMoto, tarifa_tipo, tarifa_valor, total_cobro, estado.

mensualidad:
Campos: idMensualidad, idUsuario, tipo_vehiculo, placa, fecha_inicio, fecha_fin, valor.

Relación: "pertenece a" un usuario.

Funcionalidades del Sistema
1. Autenticación de Usuarios
Descripción
El sistema permite a los usuarios iniciar sesión y registrarse utilizando credenciales de acceso. Además, valida si la cuenta está activa antes de otorgar acceso.

Casos de Uso
CU1: Un usuario registrado inicia sesión con su correo y contraseña.
CU2: Un usuario intenta iniciar sesión con credenciales incorrectas y recibe un mensaje de error.
CU3: Un usuario intenta iniciar sesión con una cuenta inactiva y se le deniega el acceso.
CU4: Un nuevo usuario se registra proporcionando nombre, correo y contraseña.

**Flujos de Trabajo**
El usuario ingresa sus credenciales.
El sistema verifica la existencia del usuario en la base de datos.
Si las credenciales son correctas y la cuenta está activa, el usuario es autenticado.
Si los datos son incorrectos o la cuenta está inactiva, se muestra un mensaje de error.

Código Relacionado
Login

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = Usuario::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return back()->withErrors(['email' => 'Datos incorrectos'])->withInput();
    }
    
    if ($user->estado != 'activo') {
        return back()->withErrors(['email' => 'Tu cuenta está inactiva.'])->withInput();
    }

    Auth::login($user);
    return redirect()->route('dashboard');
}

Registro

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
        'estado' => 'activo',
    ]);

    return response()->json(['message' => 'Registro exitoso', 'user' => $user]);
}

2. Gestión de Vehículos
Descripción
Permite buscar información de vehículos registrados en el sistema utilizando la placa del carro.


**Casos de Uso**
CU5: Un usuario busca un vehículo por su número de placa.
CU6: Un usuario introduce una placa inexistente y recibe un mensaje de error.

**Flujos de Trabajo**
El usuario ingresa la placa del vehículo.
El sistema consulta la base de datos y devuelve la información correspondiente.
Si la placa no existe, se muestra un mensaje de error.

Código Relacionado

public function buscarPorPlaca(Request $request)
{
    $placa = strtoupper($request->input('placa'));
    
    if (empty($placa)) {
        return response()->json(['error' => 'Debe ingresar una placa'], 400);
    }

    $carros = Carro::where('placaCarro', 'LIKE', "%$placa%")->get();
    
    return response()->json(['carros' => $carros]);
}

3. Cálculo de Tarifas de Estacionamiento
Descripción
El sistema permite calcular el cobro de estacionamiento basado en la tarifa seleccionada y el tiempo transcurrido.

**Casos de Uso**
CU7: Un usuario ingresa la hora de entrada y la tarifa seleccionada.
CU8: El sistema calcula el monto a pagar basado en el tiempo transcurrido.

**Flujos de Trabajo**

El usuario ingresa la hora de entrada del vehículo.
El usuario selecciona la tarifa (por hora o tarifa fija de 12 horas).
El sistema calcula el monto basado en la tarifa seleccionada.
Se muestra el monto total a pagar.

Código Relacionado

use Illuminate\Support\Facades\Config;

public function calcularCobro(Request $request)
{
    $tarifaValor = Config::get("tarifas.{$request->tarifaSeleccionada}");
    
    $entrada = Carbon::parse($request->horaEntrada);
    $salida = Carbon::now();
    $minutosTotal = $entrada->diffInMinutes($salida);
    
    $horas = max(1, round($minutosTotal / 60, 2));
    $monto = $horas * $tarifaValor;
    
    return response()->json(['monto' => $monto]);
}

