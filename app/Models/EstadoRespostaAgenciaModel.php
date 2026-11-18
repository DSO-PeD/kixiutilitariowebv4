<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoRespostaAgenciaModel extends Model
{
    //
    protected $table = 'tblEstadosRespostaAgencia';
    protected $primaryKey = 'id';
    protected $fillable = ['descricao', 'cor_hex', 'ativo'];
}
