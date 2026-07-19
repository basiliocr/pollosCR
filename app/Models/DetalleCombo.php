<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleCombo extends Model
{
    protected $table = 'detalle_combos';

    protected $fillable = ['combo_id', 'producto_id', 'cantidad'];

    public function combo(): BelongsTo
    {
        return $this->belongsTo(Combo::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}