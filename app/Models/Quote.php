<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $casts = [
        'viajantes' => 'array',
        'avisos' => 'array',
    ];

    protected $fillable = [
        'destino',
        'dias_cobrados',
        'data_inicio',
        'data_fim',
        'viajantes',
        'avisos',
        'desconto_grupo_percentual',
        'total_final'
    ];
}
