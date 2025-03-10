<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuario extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios'; 
    protected $primaryKey = 'idUsuario';

    protected $fillable = [
        'nombre', 
        'email',
        'password',
        'rol',
    ];

    protected $hidden = [
        'password',
        'remember_token', 
    ];

    public $timestamps = true; 

    /**
     * Métodos de JWTAuth
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
