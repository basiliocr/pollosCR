<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditoriaLog extends Model
{
    protected $table = 'auditoria_logs';

    protected $fillable = ['user_id', 'accion', 'tabla_afectada', 'registro_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}