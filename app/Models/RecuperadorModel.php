<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RecuperadorModel extends Model
{
    //

    protected $table = 'recuperador';
    protected $fillable = [
        'id',
        'nome_recuperador', 'contacto',  'banco', 'numero_conta', 'iban'
    ];

    public static function  getRecuperadores($base){
        $base = "'".$base."'";
//dd($base);
        $recuperadores = DB::select("CALL PKxListaRecuperadores(".$base.")");

        return $recuperadores;
    }
}
