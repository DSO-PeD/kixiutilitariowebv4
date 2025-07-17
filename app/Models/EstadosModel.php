<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EstadosModel extends Model
{
    //
    protected $table = 'estado';

    public static function getBancosContas()
    {

        return TKxBancoContaModel::all();
    }

    public static function getEstadosDCF($habilita)
    {

        $contas = DB::table('estado')
            ->where(function ($query) use ($habilita) {
                $query->where('habilita', $habilita)
                    ->orWhere('habilita', 'DCF-DOP');
            })
            ->get();
        return $contas;
    }
    public static function getEstadosRecuperacao($estadoArray)
    {
        $estadoArray = explode(',', $estadoArray);

        $contas = DB::table('estado')
            ->whereIn('id', $estadoArray)
            ->get();
        return $contas;
    }


    public static function getEstados()
    {
        return DB::table('estado')
            ->select('id', 'descricao_estado')
            ->where('id', '<', 7)
            ->get();
    }


}
