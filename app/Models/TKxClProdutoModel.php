<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TKxClProdutoModel extends Model
{
    protected $table = 'tkxclprodutos';
    protected $fillable = [
        'Metodologia',
        'PoAgrupado ',
        'Estado',
        'TipoPagamentoTaxaProcessamento',
        'PercTaxaProcessamento',
        'ValorAKZAdicionalTXProcessamento',
        'TipoPagamentoTaxaImprevisto',
        'PercTaxaImprevisto',
        'PercTaxaIva',
        'ValorAKZTaxaDeConfirmacao',
        'TipoProduto',
        'DiasMaximoRegistroComprovativo'
    ];

    public static function getProdutos()
    {

        $produtos = DB::table('tkxclprodutos')
            ->where('Estado', 1)->get();

        return $produtos;
    }
    public static function getProdutosDesembolsos()
    {

        $produtos = DB::table('tkxclprodutos')
            ->where('Estado', 1)->where('TipoProduto', 'L')->get();

        return $produtos;
    }
}
