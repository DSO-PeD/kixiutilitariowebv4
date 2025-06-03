<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class TKxBancoContaModel extends Model
{

    protected $table = 'tkxclbancoconta';
    public static function getBancosContas(){

        return TKxBancoContaModel::all();
    }

    public static function getBancosContasByBanco($BaCodigo){

        $contas= DB::table('tkxclbancoconta')
        ->where('BaCodigo', $BaCodigo)->get();

        return $contas;

    }
}
