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

    public static function getCapitalEJuros($ValorDoCredito,$TempoCredito,$TaxaMensal)
    {
        $Capital = $ValorDoCredito / $TempoCredito;

        $JuroMensal = (($ValorDoCredito * ($TaxaMensal * $TempoCredito))/100) / $TempoCredito;
        
        return [
            'Capital' => $Capital,
            'JuroMensal' => $JuroMensal
        ];
    }

    /**
     * Calcular o capital e juros para um determinado montante dde comprovativo
    */
    public static function calcularCapitalEJuros($BuMontante, $BuDadoOrigem, $comprovativoId){
    
        $extrato = TKxExtratoModel::where('Lnr', $BuDadoOrigem)
                                    ->firstOrFail(['TaxaMensal', 'TempoCredito', 'ValorCapital', 'ValorJuroMensal']);

        if (
            is_null($extrato->TaxaMensal) ||
            is_null($extrato->TempoCredito) ||
            is_null($extrato->ValorCapital) ||
            is_null($extrato->ValorJuroMensal)
        ) {
            return 0;
        }
        
        //Valor do Capital + Juros deste cliente no respecctivo produto e montante
        $CapitalEJuros = $extrato->ValorCapital + $extrato->ValorJuroMensal;
        $TotalCapital = $extrato->ValorCapital * $extrato->TempoCredito;
        $TotalJuro = $extrato->ValorJuroMensal * $extrato->TempoCredito;

        // Acumulado de comprovativos anteriores (exclui o registo atual)
        $anteriores = DB::table('comprovativos')
                            ->where('BuDadoOrigem', $BuDadoOrigem)
                            ->where('id', '!=', $comprovativoId)
                            ->selectRaw('COALESCE(SUM(Capital),0) as capitalPago, COALESCE(SUM(Juros),0) as juroPago, COUNT(*) as parcelasEmitidas')
                            ->first();

    
        $capitalPago      = $anteriores->capitalPago;
        $juroPago         = $anteriores->juroPago;
        $parcelasEmitidas = $anteriores->parcelasEmitidas;

        $restanteCapital = max($TotalCapital - $capitalPago, 0);
        $restanteJuro    = max($TotalJuro - $juroPago, 0);

        // Já atingiu os limites totais, não faz nada
        if ($restanteCapital <= 0 && $restanteJuro <= 0) {
            return 0;
        }

        $jurosDevidoAteAgora = min(($parcelasEmitidas + 1) * $extrato->ValorJuroMensal, $TotalJuro);
        $jurosEmAtraso       = max($jurosDevidoAteAgora - $juroPago, 0);

        $novoJuro    = min($BuMontante, $jurosEmAtraso, $restanteJuro);
        $novoCapital = min($BuMontante - $novoJuro, $restanteCapital);

        DB::table('comprovativos')
            ->where('id', $comprovativoId)
            ->update([
                'Juros' => $novoJuro,
                'Capital' => $novoCapital
            ]);

        return ['Juros' => $novoJuro, 'Capital' => $novoCapital];
    }
}
