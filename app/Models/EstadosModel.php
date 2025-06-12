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
            ->where('habilita', $habilita)->get();

        return $contas;
    }

    public static function getEstados()
    {
        return DB::table('estado')
            ->select('id', 'descricao_estado')
            ->where('id', '<', 7)
            ->get();
    }

    public static function getEstadosRecupecao($ids_estados_view)
    {
        return DB::table('estado')
            ->select('id', 'descricao_estado')
            ->whereIn('id', [$ids_estados_view])
            ->get();
    }
}
