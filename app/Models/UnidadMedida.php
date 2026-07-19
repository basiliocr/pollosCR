<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadMedida extends Model
{
    protected $table = 'unidades_medida';

    protected $fillable = ['nombre', 'abreviatura', 'tipo', 'equivalencia_base'];

    protected $casts = ['equivalencia_base' => 'decimal:4'];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'unidad_id');
    }

    public function convertirA(UnidadMedida $destino, float $cantidad): float
    {
        if ($this->tipo !== $destino->tipo) {
            throw new \InvalidArgumentException(
                "No se puede convertir de {$this->tipo} a {$destino->tipo}."
            );
        }

        $enBase = $cantidad * (float) $this->equivalencia_base;

        return $enBase / (float) $destino->equivalencia_base;
    }
}