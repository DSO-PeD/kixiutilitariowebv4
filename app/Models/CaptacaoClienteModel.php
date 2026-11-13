<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CaptacaoClienteModel extends Model
{ use HasFactory;

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
    ];

    protected $casts = [
        'valor_solicitado'           => 'decimal:2',
        'id_tipo_negocio'            => 'integer',
        'id_estado_resposta_agencia' => 'integer',
        'OfCodigoAgencia'            => 'integer',
        'data_captacao'              => 'date:Y-m-d',
    ];

    public function tipoNegocio()
    {
        return $this->belongsTo(TipoNegocioModel::class, 'id_tipo_negocio', 'id');
    }

    public function estadoResposta()
    {
        return $this->belongsTo(EstadoRespostaAgenciaModel::class, 'id_estado_resposta_agencia', 'id');
    }
}
