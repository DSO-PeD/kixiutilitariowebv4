<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TKxClProdutoModel extends Model
{

    public static function getProdutos(){

        $produtos = DB::table('tkxclprodutos')
        ->where('Estado', 1)->get();

        return $produtos;
    }
     public static function getProdutosDesembolsos(){

        $produtos = DB::table('tkxclprodutos')
        ->where('Estado', 1)->where('TipoProduto','L')->get();

        return $produtos;
    }
}
