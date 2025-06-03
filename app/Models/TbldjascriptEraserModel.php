<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TbldjascriptEraserModel extends Model
{
    //

    protected $table = 'tbldjascript_eraser';
    protected $fillable = [
        'id',
        'script_recuperacao',
        'UtCodigoEliminou',
        'DataEliminacao',
        'Motivo'
    ];
}
