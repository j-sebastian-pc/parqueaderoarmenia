<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensualidad extends Model
{
    protected $table = 'mensualidad'; // Nombre de la tabla
    protected $primaryKey = 'idMensualidad'; // Clave primaria
    public $timestamps = false; // Si no usas created_at y updated_at

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'idUsuario',
        'tipo_vehiculo',
        'placa',
        'fecha_inicio',
        'fecha_fin',
        'valor',
        'nombreMensualidad',
        'placaMensualidad',
        'telefonoMensualidad',
        'entradaMensualidad'
    ];
}