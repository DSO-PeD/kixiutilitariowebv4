<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferenciaPGTModel extends Model
{
    //

    protected $table = 'referenciasmanuais';

    protected $fillable = [
        'id',
        'BuDadoOrigem',
        'nomecliente',
        'telefone',
        'PoCodigo',
        'tipo',
        'referencia',
        'inicio',
        'fim',
        'montante',
        'estado',
        'activo',
        'BaseOperacao',
        'Eliminado'
    ];
}
