<?php

namespace App\Models;

 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblCaptacaoClienteDetalheModel extends Model
{
   use HasFactory;

    protected $table = 'tblCaptacaoClienteDetalhe';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'id_captacao_cliente',
        'id_estado_resposta_agencia',
        'data_rejeicao',
        'razao_rejeicao',
        'id_quem_rejeitou',
        'idade',
        'numero_bi',
        'observacoes',
        'UtCodigoOficial',
        'OfCodigoAgencia',
    ];

    protected $casts = [
        'id_captacao_cliente'        => 'integer',
        'id_estado_resposta_agencia' => 'integer',
        'id_quem_rejeitou'           => 'integer',
        'idade'                      => 'integer',
        'OfCodigoAgencia'            => 'integer',
        'data_rejeicao'              => 'date:Y-m-d',
    ];

    public function captacaoCliente()
    {
        return $this->belongsTo(CaptacaoClienteModel::class, 'id_captacao_cliente', 'id');
    }

    public function entidadeRejeicao()
    {
        return $this->belongsTo(EntidadesRejeicaoModel::class, 'id_quem_rejeitou', 'id');
    }
}
