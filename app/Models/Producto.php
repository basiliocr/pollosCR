<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'codigo',
        'nombre',
        'precio',
        'categoria_id',
        'unidad_id',
        'requiere_receta',
        'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'requiere_receta' => 'boolean',
        'activo' => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_id');
    }

    public function inventario(): HasMany
    {
        return $this->hasMany(Inventario::class);
    }

    /** Ingredientes que componen este producto (si es un plato preparado). */
    public function receta(): HasMany
    {
        return $this->hasMany(Receta::class, 'producto_id');
    }

    /** Recetas donde este producto figura como insumo de otro plato. */
    public function usadoEnRecetas(): HasMany
    {
        return $this->hasMany(Receta::class, 'insumo_id');
    }
}