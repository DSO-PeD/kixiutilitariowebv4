<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoNegocioModel extends Model
{
    //
    protected $table = 'tblTiposNegocio';
    protected $primaryKey = 'id';
    protected $fillable = ['descricao', 'ativo'];
}
