<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moto extends Model
{
    protected $table = 'motos';
    protected $primaryKey = 'idMoto';
    
    // Desactivar timestamps
    public $timestamps = false;
    
    protected $fillable = [
        'placaMoto',
        'cascoMoto',
        'moduloCascoMoto',
        'entradaMoto',
        'salidaMoto',
        'tarifa_tipo',
        'tarifa_valor',
        'total_cobro',
        'estado',
        'hora'
    ];
}