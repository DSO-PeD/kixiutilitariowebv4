<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TblRDprintRiscoExposicaoCentrosCustoModel extends Model
{
    use HasFactory;

    /**
     * Nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'tblRDRiscoExposicaoCentrosCusto';

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
        'PosicaoRisco',
        'CentroCusto',
        'Provincia',
        'TotalClientesAtivos',
        'ClientesEmAtraso',
        'CarteiraTotalCapital',
        'CarteiraVencidaCapital',
        'PAR_Percentual',
        'ProvisaoTotal',
        'MaxDiasAtraso',
        'DataProcessamento',
        'Activo',
    ];

    /**
     * Conversões de tipos de dados de atributos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ID'                     => 'integer',
        'DataReferencia'         => 'date',
        'PosicaoRisco'           => 'integer',
        'TotalClientesAtivos'    => 'integer',
        'ClientesEmAtraso'       => 'integer',
        'CarteiraTotalCapital'   => 'decimal:2',
        'CarteiraVencidaCapital' => 'decimal:2',
        'PAR_Percentual'         => 'decimal:4',
        'ProvisaoTotal'          => 'decimal:2',
        'MaxDiasAtraso'          => 'integer',
        'DataProcessamento'      => 'datetime',
        'Activo'                 => 'integer',
    ];
}
