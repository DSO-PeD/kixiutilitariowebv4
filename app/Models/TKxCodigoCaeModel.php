<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TKxCodigoCaeModel extends Model
{
    public static function getActividadeEconomica()
    {

        $ae = DB::table('tkxcodigoae')->orderBy('caeDesignacao', 'ASC')
            ->where('Seleccionar', 1)
            ->get();

        return $ae;

    }
    public static function getGrupoActividadeEconomica()
    {
         return DB::table('tkxcodigoae')
        ->select('sectorGrupo')
        ->where('Seleccionar', 1)
        ->whereNotNull('sectorGrupo')
        ->where('sectorGrupo', '<>', '') // remove strings vazias
        ->whereRaw("LTRIM(RTRIM(sectorGrupo)) <> ''") // remove apenas espaços em branco
        ->distinct()
        ->orderBy('sectorGrupo', 'ASC')
        ->get();
    }
}
