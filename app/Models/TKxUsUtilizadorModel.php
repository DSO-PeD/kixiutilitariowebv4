<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class TKxUsUtilizadorModel extends Authenticatable
{
    use Notifiable;
    protected $table = 'tkxusutilizador';
protected $primaryKey = 'UtCodigo';
public $incrementing = false;
protected $keyType = 'string';

    protected $fillable = [
        'UtCodigo',
        'UtNome',
        'UtFuncao',
        'UtAgencia',
        'rec_confirma',
        'rec_viewestados',
        'rec_comprovativo',
        'rec_extrato',
        'rec_subsidio',
        'view_pendentes',
        'elimina_confirmado_exportado',
        'reconci_habilita',
        'nova_recuperacao',
        'mn_tesouraria',
        'mn_recuperacoesDCF',
        'recuperacao_estados_activos',
        'recuperacao_estados_operacao',
        'recuperacao_estados_anterior',
        'activo',
        'ip_utilizador',
        'remember_token',
        'UtSenha',
        'OfCodigo',
        'OfIdentificador',
        'OfNombre',
        'BasesOperacao',
        'DataFecho',
        'NumeroRegistroTabela'
    ];

    protected $hidden = [
        'UtSenha',
    ];

    public function getAuthPassword()
    {
        return $this->UtSenha;
    }

    public static function getDJAIP()
    {
        return DB::table('tkxusutilizador')
            ->where('activo', 1)
            ->pluck('ip_utilizador')
            ->toArray();
    }

    public static function getDJAIPKIXILAN()
    {
        return DB::table('ipfaixas')
            ->pluck('cidr')
            ->toArray();
    }
}
