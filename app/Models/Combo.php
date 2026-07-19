<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Combo extends Model
{
    protected $table = 'combos';

    protected $fillable = ['nombre', 'precio', 'activo'];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleCombo::class);
    }
}