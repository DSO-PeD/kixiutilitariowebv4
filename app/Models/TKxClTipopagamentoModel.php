<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TKxClTipopagamentoModel extends Model
{
    //

    protected $table = 'tkxcltipopagamento';
    public static function getFormasDePamentos(){

        return TKxClTipopagamentoModel::all();
    }


}
