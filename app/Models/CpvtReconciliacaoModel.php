<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpvtReconciliacaoModel extends Model
{
    //

    protected $table = 'cpvtreconciliacao';
    protected $fillable = [
        'id',
        'datareconciliacao',
        'CodigoConta',
        'voucher',
        'descricao',
        'observacao',
        'idcomprovativo',
        'UtCodigo',
        'idestado'
    ];
}
