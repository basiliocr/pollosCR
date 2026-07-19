<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleVenta extends Model
{
    protected $table = 'detalle_ventas';

    protected $fillable = ['venta_id', 'producto_id', 'combo_id', 'cantidad', 'subtotal'];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'subtotal' => 'decimal:2',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function combo(): BelongsTo
    {
        return $this->belongsTo(Combo::class);
    }
}