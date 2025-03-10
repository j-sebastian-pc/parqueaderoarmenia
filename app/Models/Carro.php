<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carro extends Model
{
    protected $table = 'carros';
    protected $primaryKey = 'idCarro';
    
    protected $fillable = [
        'placaCarro', 
        'entradaCarro', 
        'salidaCarro',
        'tarifa_tipo',
        'tarifa_valor',
        'total_cobro',
        'estado',
        'hora',
        'cobro'
    ];
    
    
    public $timestamps = true;
}