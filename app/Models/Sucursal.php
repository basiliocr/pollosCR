<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    protected $fillable = ['nombre', 'direccion', 'activa'];

    protected $casts = ['activa' => 'boolean'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}