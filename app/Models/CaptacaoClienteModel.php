<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CaptacaoClienteModel extends Model
{
    use HasFactory;

    protected $table = 'tblCaptacoesClientes';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nome_cliente_empresa',
        'genero',
        'local_negocio',
        'valor_solicitado',
        'id_tipo_negocio',
        'telefone',
        'data_captacao',
        'id_estado_resposta_agencia',
        'OfCodigoAgencia',
        'UtCodigoCaptador',
        'UtCodigoGerente',
        'data_atribuicao_gerente',
        'UtCodigoOficialCredito',
        'data_atribuicao_oficial',
        'atribuido_por',
        'ObservacaoEstado'
    ];

    protected $casts = [
        'valor_solicitado'           => 'decimal:2',
        'id_tipo_negocio'            => 'integer',
        'id_estado_resposta_agencia' => 'integer',
        'OfCodigoAgencia'            => 'integer',
        'data_captacao'              => 'date:Y-m-d',
        'data_atribuicao_gerente'    => 'datetime',
        'data_atribuicao_oficial'    => 'datetime',
    ];

    // --- RELACIONAMENTOS ---

    public function tipoNegocio()
    {
        return $this->belongsTo(TipoNegocioModel::class, 'id_tipo_negocio', 'id');
    }

    public function estadoResposta()
    {
        return $this->belongsTo(EstadoRespostaAgenciaModel::class, 'id_estado_resposta_agencia', 'id');
    }

    public function gerente()
    {
        return $this->belongsTo(TKxUsUtilizadorModel::class, 'UtCodigoGerente', 'UtCodigo');
    }

    public function oficialCredito()
    {
        return $this->belongsTo(TKxUsUtilizadorModel::class, 'UtCodigoOficialCredito', 'UtCodigo');
    }

    public function captador()
    {
        return $this->belongsTo(TKxUsUtilizadorModel::class, 'UtCodigoCaptador', 'UtCodigo');
    }

    // --- MÉTODOS DE ATRIBUIÇÃO ---

    /**
     * Atribui um lote de clientes ao Gerente da agência
     */
    public static function atribuirGerente(array $clientesIds, string $utCodigoGerente, string $atribuidoPor): int
    {
        return self::whereIn('id', $clientesIds)->update([
            'UtCodigoGerente'         => $utCodigoGerente,
            'data_atribuicao_gerente' => Carbon::now(),
            'atribuido_por'           => $atribuidoPor,
        ]);
    }

    /**
     * Atribui um lote de clientes a um Oficial de Crédito
     */
    public static function atribuirOficial(array $clientesIds, string $utCodigoOficial, string $atribuidoPor): int
    {
        return self::whereIn('id', $clientesIds)->update([
            'UtCodigoOficialCredito'         => $utCodigoOficial,
            'data_atribuicao_oficial' => Carbon::now(),
            'atribuido_por'           => $atribuidoPor,
        ]);
    }
}
