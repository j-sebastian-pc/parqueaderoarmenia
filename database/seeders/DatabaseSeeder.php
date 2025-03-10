<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::factory()->count(10)->create(); // Crear 10 usuarios de prueba
    }
}
