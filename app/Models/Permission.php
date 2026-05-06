<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'label', 
    ];

    // Relacionamento inverso (opcional)
    public function usutilizador()
    {
        return $this->belongsToMany(
            TKxUsUtilizadorModel::class,
            'tkxusutilizador_permissions',
            'permission_id',
            'UtCodigo'
        );
    }
}
