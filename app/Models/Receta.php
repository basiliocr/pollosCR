<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receta extends Model
{
    protected $table = 'recetas';

    protected $fillable = ['producto_id', 'insumo_id', 'cantidad', 'unidad_id'];

    protected $casts = ['cantidad' => 'decimal:3'];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'insumo_id');
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_id');
    }
}