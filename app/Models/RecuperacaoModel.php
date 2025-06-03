<?php

namespace App\Models;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class RecuperacaoModel extends Model
{
    protected $table = 'recuperacao';
    protected $fillable = [
        'id',
        'CiFecha',
        'UtCodigo',
        'ReBuDadoOrigem',
        'ReBuReferencia',
        'ReBuMontante',
        'ReBuData',
        'ReBuDataLPF',
        'id_recuperador',
        'id_comprovativo',
        'id_estado',
        'Eliminado',
        'UtCodigoEliminou',
        'DataEliminacao',
        'Motivo',
        'BaseOperacao',
        'estado',
        'color',
        'dias_epe'
    ];

    public static function listarBasesDeConsultaRecuperacao()
    {
        $bases = DB::select("SELECT * FROM tkxagencias where BasesOperacao is not null");

        return $bases;
    }

    public static function listarEstadoDeConsultaUsuario($rec_viewestado)
    {
        $EstadosPermitidos = DB::table('estado')->whereIn('estado.id', $rec_viewestado)->get();
        return $EstadosPermitidos;
    }

    public static function listarVoucherParaRecuperacao($Bases, $permissao_user)
    {

        $listaVOUCHER_recuparacao = DB::select("CALL PKxUListarVoucherParaRecuperacao(" . $Bases . ",'" . $permissao_user . "')");
        return $listaVOUCHER_recuparacao;
    }

    public static function getResumoReportRecuperaco($Base)
    {
        $ResumoReportRecuperaco = array();
        $ResumoRegistrosAntigos = DB::select("SELECT count(id) AS ResumoRegistrosAntigos from recuperacao WHERE id_estado=4 AND eliminado=0 and BaseOperacao IN(" . $Base . ")");

        $ResumoReportRecuperaco[0] = $ResumoRegistrosAntigos[0]->ResumoRegistrosAntigos;

        $ResumoAnalisePorPagar = DB::select("SELECT count(id) AS ResumoAnalisePorPagar from recuperacao where id_estado=5 AND eliminado=0 and BaseOperacao IN(" . $Base . ")");



        $ResumoReportRecuperaco[1] = $ResumoAnalisePorPagar[0]->ResumoAnalisePorPagar;


        $ResumoTotalRegistos = DB::select("SELECT count(id) AS ResumoTotalRegistos from recuperacao WHERE eliminado=0  and id_estado in (1,2,4,5,6) and BaseOperacao IN(" . $Base . ")");



        $ResumoReportRecuperaco[2] = $ResumoTotalRegistos[0]->ResumoTotalRegistos;


        return $ResumoReportRecuperaco;
    }

    public static function getDadosRecuperadores($Bases)
    {
        $DadosRecuperadores = DB::select("CALL PKxRprtRecuperadores(" . $Bases . ")");
        return $DadosRecuperadores;
    }

    public static function getDadosRecuperadoreRegistosAntigos($Base)
    {

        $DadosRecuperadores = DB::table('recuperacao')
            ->join('recuperador', 'recuperacao.id_recuperador', '=', 'recuperador.id')
            ->select('recuperacao.ReBuDadoOrigem', 'recuperador.nome_recuperador', 'recuperador.Imagen', 'recuperador.UtCodigo', 'recuperador.id as foto', 'recuperacao.ReBuDataLPF')
            ->whereIn('recuperacao.BaseOperacao', $Base)
            ->where('recuperacao.id_estado', 4)
            ->where('recuperacao.Eliminado', 0)
            ->orderBy('recuperador.nome_recuperador', 'asc')
            ->get();

        return $DadosRecuperadores;
    }
    public static function getDadosRecuperadoreRegistosApagar($Base)
    {

        $DadosRecuperadores = DB::table('recuperacao')
            ->join('recuperador', 'recuperacao.id_recuperador', '=', 'recuperador.id')
            ->select('recuperacao.ReBuDadoOrigem', 'recuperador.nome_recuperador', 'recuperador.Imagen', 'recuperador.UtCodigo', 'recuperador.id as foto', 'recuperacao.ReBuDataLPF')
            ->whereIn('recuperacao.BaseOperacao', $Base)
            ->where('recuperacao.id_estado', 5)
            ->where('recuperacao.Eliminado', 0)
            ->orderBy('recuperador.nome_recuperador', 'asc')
            ->get();

        return $DadosRecuperadores;
    }

    //Funcão: listar todas recuperações Owner: WSA 
    public static function getRecuperacoes($tipo,$estado = null,$agencia = null,$dataInicio = null,$dataFim = null)
    { 
        //Pega a base do utilizador logado, pois a recuperação é em função disso, cada um vê a base em tutela, somente os UtAgencia=0 vêem todas
        //$base = TKxAgenciaModel::getAgencias(Auth::user());
        //$base = $base[0]->OfIdentificador;

        $query = DB::table('recuperacao as rec')
                    ->join('estado as est','est.id','=','rec.id_estado')
                    ->join('recuperador as r','r.id','=','rec.id_recuperador')
                    ->join('tkxusutilizador as util','util.UtCodigo','=','rec.UtCodigo')
                    ->join('comprovativos as comp','comp.id','=','id_comprovativo');
                
                    if ($tipo == 3 && $dataInicio && $dataFim) { //Tipo: 1 vem do filtro Estado e Agencia | Tipo: 3 vem do filtro em função das datas 
                        $query->whereBetween('rec.CiFecha', [$dataInicio, $dataFim]);
                        $query->where('rec.id_estado', $estado);
                        if(Auth::user()->UtAgencia >0){ //Utilizador com Agencia única p/ex.: UE,MB,HH, etc...
                            $query->where('rec.BaseOperacao',$agencia);
                        }else{ //Utilizador Adm que vê todas agencias
                            if($agencia !='KC'){
                                $query->where('rec.BaseOperacao',$agencia);
                            }
                        }
                    } else {
                        if ($estado) {
                            $query->where('rec.id_estado', $estado);
                        }
                        if ($agencia && $agencia !='KC') {
                            $query->where('rec.BaseOperacao', $agencia);
                        }
                    }

                    return $query->select(
                            'rec.id',
                            'rec.CiFecha',
                            'util.UtCodigo',
                            'util.UtNome',
                            'rec.ReBuDadoOrigem',
                            'rec.ReBuReferencia',
                            'rec.ReBuMontante',
                            'rec.ReBuData',
                            'rec.ReBuDataLPF',
                            'r.nome_recuperador',
                            'rec.id_comprovativo',
                            'est.descricao_estado as estado',
                            'rec.id_estado',
                            'rec.BaseOperacao',
                            'rec.obs',
                            'util.obs_regista',
                            'comp.infoadicional',
                            'est.color as color',
                            'est.fa_icon',
                            'est.dias_epe',
                            'est.elimina_registro'
                        )
                        ->paginate(10);
    }
    
    //Funcão: pegar montante e quantidade de recuperações de uma agencia e estado especifico Owner: WSA 
    public static function getRecuperacoesEstatistica($tipo,$estado,$agencia,$dataInicio,$dataFim)
    {
        //Pega a base do utilizador logado, pois a recuperação é em função disso, cada um vê a base em tutela, somente os UtAgencia=0 vêem todas
        //$agenciaBase = TKxAgenciaModel::getAgencias(Auth::user());
        //$agenciaBase = $agenciaBase[0]->OfIdentificador;
        
        if($tipo==3){
            if(Auth::user()->UtAgencia >0){
                $query = DB::table('recuperacao as rec')
                        ->whereBetween('rec.CiFecha', [$dataInicio,$dataFim])
                        ->where('rec.BaseOperacao',$agencia)
                        ->where('rec.id_estado',$estado); 
            }else{
                $query = DB::table('recuperacao as rec')
                        ->whereBetween('rec.CiFecha', [$dataInicio,$dataFim])
                        ->where('id_estado',$estado);
                        if($agencia !='KC'){
                            $query->where('rec.BaseOperacao',$agencia);
                        } 
            }            
        }else{
            if($agencia !='KC'){ //KC significa filtragem para todas bases Kixicredito
                $query = DB::table('recuperacao as rec')
                            ->where('rec.id_estado', $estado)
                            ->where('rec.BaseOperacao', $agencia);
            }else{
                $query = DB::table('recuperacao as rec')
                            ->where('rec.id_estado', $estado);
            }        
        }
        
        return [
            'total' => $query->count(),
            'somaMontante' => $query->sum('rec.ReBuMontante')
        ];
    }

    //Funcão Owner: WSA 
    public static function getRecuperacoesParaMudarEstado()
    {
        //$recuperacao = DB::select("CALL PKxURecuperacoesParaMudarEstado(" . $idRecuperacao . ")");
        //return $recuperacao;
        return DB::table('recuperacao as rec')->where('rec.id_estado',1)
                    ->join('estado as est','est.id','=','rec.id_estado')
                    ->join('recuperador as r','r.id','=','rec.id_recuperador')
                    ->join('tkxusutilizador as util','util.UtCodigo','=','rec.UtCodigo')
                    ->join('comprovativos as comp','comp.id','=','id_comprovativo')
                    ->where('rec.id_estado',1)
                    ->select(
                        'rec.CiFecha',
                        'rec.id',
                        'util.UtCodigo',
                        'util.UtNome',
                        'rec.ReBuDadoOrigem',
                        'rec.ReBuReferencia',
                        'rec.ReBuMontante',
                        'rec.ReBuData',
                        'rec.ReBuDataLPF',
                        'r.nome_recuperador',
                        'r.id as reCuperadorUtCodigo',
                        'r.UtCodigo as UtCodigoRecuperador',
                        'r.Localidade',
                        'rec.id_comprovativo',
                        'est.descricao_estado as estado',
                        'rec.id_estado',
                        'rec.BaseOperacao',
                        'rec.obs',
                        'util.obs_regista',
                        'comp.infoadicional',
                        'est.color as color',
                        'est.fa_icon',
                        'est.dias_epe',
                        'est.elimina_registro'
                    )
                    ->get();
    }

    public static function getComprovativosPorDataRegistroBaseTodas($Bases, $DataInicio, $DataFim, $estadoAConsultar)
    {
        $recuperacao = DB::select("CALL PKxUComprovativosPorDataRegistroBaseTodas(" . $Bases . ",'" . $DataInicio . "','" . $DataFim . "'," . $estadoAConsultar . ")");


        return $recuperacao;
    }


    public static function setEliminarRecuperacao($id, $UtCodigo, $DataEliminacao, $MotivoEliminacao)
    {
        $status = false;

        /* $affectedEstadoeliminado = DB::table('recuperacao')
            ->where('id', $id)
            ->delete();*/


        $affectedEstadoeliminado = DB::table('recuperacao')
            ->where('id', $id)
            ->update(['id_estado' => 6]);



        if ($affectedEstadoeliminado) {
            $status = true;
        } else {
            $status = false;
        }


        $affectedeQuemEliminou = DB::table('recuperacao')
            ->where('id', $id)
            ->update(['UtCodigoEliminou' => $UtCodigo]);

        if ($affectedeQuemEliminou) {
            $status = true;
        } else {
            $status = false;
        }

        $affectedeMotivo = DB::table('recuperacao')
            ->where('id', $id)
            ->update(['DataEliminacao' => $DataEliminacao]);

        if ($affectedeMotivo) {
            $status = true;
        } else {
            $status = false;
        }

        $affectedeMotivo = DB::table('recuperacao')
            ->where('id', $id)
            ->update(['Motivo' => $MotivoEliminacao]);

        if ($affectedeMotivo) {
            $status = true;
        } else {
            $status = false;
        }

        return $status;
    }

    //Funcão Owner: WSA 
    public static function mudarEstadoParaExportado($id, $sql)
    {   
        $statusU = false;
        $affectedeMotivo = DB::table('recuperacao')
            ->where('id', $id)
            ->where('id_estado', 1)
            ->update(['id_estado' => 2, 'isertexportado' => $sql]);

        if ($affectedeMotivo) {
            $statusU = true;
        }
        return $statusU;
    }

    public static function mudarEstadoParaConfirmado($id)
    {
        $statusU = false;



        $affectedeMotivo = DB::table('recuperacao')
            ->where('id', $id)
            ->where('id_estado', 5)
            ->update(['id_estado' => 3]);

        if ($affectedeMotivo) {
            $statusU = true;
        }

        $affectedeMotivo = DB::table('recuperacao')
            ->where('id', $id)
            ->where('id_estado', 2)
            ->update(['id_estado' => 3]);

        if ($affectedeMotivo) {
            $statusU = true;
        }


        return $statusU;
    }


    public static function validarRegistroAntigo($id)
    {
        $statusU = false;

        $affectedeMotivo = DB::table('recuperacao')
            ->where('id', $id)
            ->where('id_estado', 4)
            ->update(['id_estado' => 1]);

        if ($affectedeMotivo) {
            $statusU = true;
        } else {
            $statusU = false;
        }




        return $statusU;
    }

    public static function alterarEstadoOBS($id, $obs)
    {
        $statusU = false;

        $affectedeEstado = DB::table('recuperacao')
            ->where('id', $id)
            ->where('id_estado', 2)
            ->update([
                'id_estado' => 7,
                'obs' => $obs
            ]);

        if ($affectedeEstado) {
            $statusU = true;
        } else {
            $statusU = false;
        }




        return $statusU;
    }

    public static function verificarSeBorderouxExisteNaRecuperacao($idComprovativo)
    {
        $c = DB::table('recuperacao')
            ->where('id_comprovativo', '=', $idComprovativo)
            ->where('id_estado', '<>', 6)
            //->where('BaCodigo', '=', $BaCodigo)
            //->where('Eliminado', '=', $Eliminado)
            ->first();



        return $c;
    }
}
