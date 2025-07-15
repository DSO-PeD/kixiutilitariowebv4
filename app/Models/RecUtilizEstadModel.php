<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecUtilizEstadModel extends Model
{
    //
       protected $table = 'rec_utiliz_estad';

       protected $fillable = [
        'id',
        'UtCodigo',
        'observacoes',
        'id_recuperacao',
        'id_estado'
       ];
}
