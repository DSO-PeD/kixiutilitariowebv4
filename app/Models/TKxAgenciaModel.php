<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TKxAgenciaModel extends Model
{
    protected $table = 'tkxagencias';
    protected $fillable = [
        'OfCodigo','OfIdentificador','OfNombre','BasesOperacao','NumeroRegistroTabela'
    ];

    public static function getAgencias($user){
        $query = DB::table('tkxagencias as ag');
        
                if($user->UtAgencia > 0){
                    $query->join('tkxusutilizador as util','ag.OfCodigo','=','util.UtAgencia');
                    $query->where('util.UtCodigo',$user->UtCodigo);
                } 

                return $query->select('ag.OfCodigo','ag.OfIdentificador','ag.OfNombre','ag.BasesOperacao')
                        ->get();
    }
}
