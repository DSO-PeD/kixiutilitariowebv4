<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class TblRDRiscoTopClientesModel extends Model
{
    use HasFactory;

    /**
     * Nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'tblRDRiscoTopClientes';

    /**
     * Chave primária da tabela.
     *
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * Indica se os timestamps padrão do Laravel (created_at, updated_at) são geridos.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'DataReferencia',
        'TipoRanking',
        'Posicao',
        'TipoCliente',
        'CodigoCliente',
        'NomeCliente',
        'Produto',
        'PeCodigo',
        'ccDireccao',
        'CentroCusto',
        'DiasAtrasoACT',
        'SaldoCapitalACT',
        'SaldoJurosACT',
        'SaldoTotalEmRisco',
        'ProvisaoACT',
        'UsSectorista',
        'NomeOficialCredito',
        'DataProcessamento',
        'Activo',
    ];

    /**
     * Conversões de tipos de dados de atributos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ID'                  => 'integer',
        'DataReferencia'      => 'date',
        'Posicao'             => 'integer',
        'DiasAtrasoACT'       => 'integer',
        'SaldoCapitalACT'     => 'decimal:2',
        'SaldoJurosACT'       => 'decimal:2',
        'SaldoTotalEmRisco'   => 'decimal:2',
        'ProvisaoACT'         => 'decimal:2',
        'DataProcessamento'   => 'datetime',
        'Activo'              => 'integer',
    ];
}
