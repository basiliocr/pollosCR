<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entrega extends Model
{
    protected $table = 'entregas';

    protected $fillable = ['venta_id', 'numero_mesa', 'estado'];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }
}