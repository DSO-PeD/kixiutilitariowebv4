<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class TKxUsUtilizadorModel extends Authenticatable
{
    use Notifiable;

    protected $table = 'tkxusutilizador'; // nome da sua tabela

    protected $primaryKey = 'UtCodigo';
    public $incrementing = false; // se o UtCodigo for string

    protected $fillable = [

        'UtSenha ',
        'UtNome ',
        'UtFuncao ',
        'UtAgencia ',
        'rec_confirma ',
        'rec_exporta ',
        'rec_viewestados ',
        'rec_validaregistos_antigos ',
        'rec_registra ',
        'rec_comprovativo ',
        'rec_extrato ',
        'rec_subsidio ',
        'obs_regista ',
        'rec_habilita_comprovativo ',
        'view_pendentes ',
        'elimina_confirmado_exportado ',
        'reconci_habilita ',
        'nova_recuperacao ',
        'mn_tesouraria ',
        'mn_recuperacoesDCF ',
        'recuperacao_estados_activos ',
        'recuperacao_estados_operacao ',
        'recuperacao_estados_anterior ',
        'activo  ',
        'ip_utilizador'
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
    $ipValidos = DB::table('tkxusutilizador')
        ->where('activo', 1)
        ->pluck('ip_utilizador') // retorna apenas os valores da coluna
        ->toArray(); // converte para array

    return $ipValidos;
}

}
