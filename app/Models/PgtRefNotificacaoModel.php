<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PgtRefNotificacaoModel extends Model
{
    protected $table = 'pgtrefnotificacao';
    protected $fillable = [
        'idTransacao',
        'numLogSistema',
        'idLogSistema',
        'dataTransaccaoCliente',
        'montantePago',
        'tipoTerminal',
        'iIdentTerminal',
        'localidadeTerminal',
        'refPagamento',
        'id'

    ];
}
