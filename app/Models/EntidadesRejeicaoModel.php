<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntidadesRejeicaoModel extends Model
{
    protected $table = 'tblEntidadesRejeicao';
    protected $primaryKey = 'id';
    protected $fillable = ['sigla', 'descricao', 'cor_hex', 'ativo'];

    public function detalhesCaptacao()
    {
        return $this->hasMany(TblCaptacaoClienteDetalheModel::class, 'id_quem_rejeitou', 'id');
    }
}
