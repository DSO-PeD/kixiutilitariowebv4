<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TKxUsUtilizadorModel extends Authenticatable
{
    use Notifiable;

    protected $table = 'tkxusutilizador'; // nome da sua tabela

    protected $primaryKey = 'UtCodigo';
    public $incrementing = false; // se o UtCodigo for string

    protected $fillable = [
        'UtCodigo',
        'UtSenha',
        'UtNome',
        'UtFuncao',
        'UtAgencia',
        'rec_confirma',
        'rec_exporta',
        'rec_viewestados',
        'rec_validaregistos_antigos',
        'rec_registra',
        'rec_comprovativo',
        'rec_extrato',
        'rec_subsidio',
        'obs_regista',
        'rec_habilita_comprovativo',
        'view_pendentes',
        'elimina_confirmado_exportado'
    ];

    protected $hidden = [
        'UtSenha',
    ];

    public function getAuthPassword()
    {
        return $this->UtSenha;
    }

}
