<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HelperModel extends Model
{
    public static function splitName($fullName)
    {
        $nameParts = explode(' ', $fullName);
        $firstName = array_shift($nameParts);
        $lastName = array_pop($nameParts);

        return $firstName. ' ' . $lastName;
    }

    public static function getArrecadacaoXXX($DataI,$DataF){
        return DB::table('comprovativos as c')
        ->join('tkxclprodutos as p', 'p.Metodologia', '=', 'c.PoCodigo')
        ->join('estado as e', 'e.id', '=', 'c.idestado')
        ->select(
            DB::raw("
                CASE
                    WHEN p.TipoProduto = 'S' THEN 'S'
                    ELSE 'R'
                END as TipoProduto
            "),
            DB::raw('SUM(c.BuMontante) as total')
        )
        ->where('e.descricao_estado', 'Validado')
        ->whereBetween('c.created_at', [
            Carbon::parse($DataI)->startOfDay(),
            Carbon::parse($DataF)->endOfDay(),
        ])
        ->groupBy(DB::raw("
            CASE
                WHEN p.TipoProduto = 'S' THEN 'S'
                ELSE 'R'
            END
        "))
        ->pluck('total', 'TipoProduto');
    }

    public static function getArrecadacao($DataI, $DataF)
    {
        return DB::table('comprovativos as c')
            ->join('tkxclprodutos as p', 'p.Metodologia', '=', 'c.PoCodigo')
            ->join('estado as e', 'e.id', '=', 'c.idestado')
            ->selectRaw("
                SUM(CASE WHEN p.TipoProduto = 'S' THEN c.BuMontante ELSE 0 END) AS S,
                SUM(CASE WHEN p.TipoProduto IN ('P','L') THEN c.BuMontante ELSE 0 END) AS R
            ")
            ->where('e.descricao_estado', 'Validado')
            ->whereBetween('c.created_at', [
                Carbon::parse($DataI)->startOfDay(),
                Carbon::parse($DataF)->endOfDay(),
            ])
            ->first();
    }
}
