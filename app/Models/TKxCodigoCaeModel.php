<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TKxCodigoCaeModel extends Model
{
    public static function getActividadeEconomica(){

        $ae = DB::table('tkxcodigoae')->orderBy('caeDesignacao', 'ASC')
        ->where('Seleccionar', 1)
        ->get();

        return $ae;

    }
}
