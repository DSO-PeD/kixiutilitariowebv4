<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\TKxBancoModel;
use App\Models\EstadosModel;
use App\Models\TKxDeclaracaoModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DeclaracaoController extends Controller
{
    public function viewDeclaracoes(Request $request)
    {
        $lista_bancos = TKxBancoModel::getBancos();
        $lista_estados = EstadosModel::getEstadosDeclaracao();

        $query = DB::table('tkxpedidodeclaracao as decl')
                        ->join('tkxclbanco as banc', 'decl.banco_id', '=', 'banc.BaCodigo')
                        ->join('estado as est', 'decl.estado_id', '=', 'est.id')
                        ->select('decl.*', 'banc.BaNome', 'est.descricao_estado', 'est.color');
        
        //Filtros
        if ($request->filled('lnr')) {
            $query->where('decl.lnr', 'like', '%' . $request->lnr . '%');
        }

        if ($request->filled('nome')) {
            $query->where('decl.nome', 'like', '%' . $request->nome . '%');
        }

        if ($request->filled('documento')) {
            $query->where('decl.documento', 'like', '%' . $request->documento . '%');
        }

        if ($request->filled('estado')) {
            $query->where('decl.estado_id', $request->estado);
        }
        
        if ($request->filled('banco')) {
            $query->where('decl.banco_id', $request->banco);
        }

        $declaracoes = $query->orderByDesc('decl.id')
            ->paginate(10)
            ->withQueryString(); //Manter os filtros na URL durante a paginação
        
        return Inertia::render('Declaracoes', [
            'bancos' => $lista_bancos,
            'estados' => $lista_estados,
            'declaracoes' => $declaracoes,
            'filters' => $request->only(['nome', 'documento', 'lnr', 'estado']),
        ]);
    }

    public function viewDeclaracao($id)
    {
        $declaracao = DB::table('tkxpedidodeclaracao as decl')
                        ->join('tkxclbanco as banc', 'decl.banco_id', '=', 'banc.BaCodigo')
                        ->join('estado as est', 'decl.estado_id', '=', 'est.id')
                        ->select('decl.*', 'banc.BaNome', 'est.descricao_estado','est.color')
                        ->where('decl.id', $id)
                        ->first();
        if (!$declaracao) {
            return redirect()->back()->with('error', 'Declaração não encontrada!');
        }

        $fileUrl = $declaracao->ficheiro 
                    ? asset('storage/documentos/' . $declaracao->ficheiro)
                    : null;

        $declaracao->ficheiro = $fileUrl;

        return Inertia::render('VerDeclaracao', [
            'declaracao' => $declaracao
        ]);
    }

    public function guardarDeclaracao(Request $request)
    {
            $validatedData = $request->validate([
                'lnr' => 'required|string|max:10|regex:/^[A-Z]{2}\/\d{5}$/',
                'saving' => 'required|string|max:10|regex:/^[A-Z]{2}\/[A-Z]{1}\/\d{5}$/',
                'nome' => 'required|string|max:255',
                'documento' => 'required|string|max:20',
                'telefone' => 'required|string|max:9|min:9',
            ],[
                'lnr.max' => 'O campo LNR deve conter no máximo 10 caracteres.',
                'saving.max' => 'O campo SAVING deve conter no máximo 10 caracteres.',
                'nome.max' => 'O campo NOME deve conter no máximo 255 caracteres.',
                'documento.max' => 'O campo DOCUMENTO deve conter no máximo 20 caracteres.',
                'telefone.max' => 'O campo TELEFONE deve conter no máximo 9 caracteres.',
                'telefone.min' => 'O campo TELEFONE deve conter no mínimo 9 caracteres.',
                'lnr.regex' => 'O campo LNR deve seguir o formato AC/00000',
                'saving.regex' => 'O campo SAVING deve seguir o formato AC/I/00000',
            ]);

            //Verificar se quer registar
            $exists = TKxDeclaracaoModel::where('lnr', $request->lnr)
                    ->whereDate('created_at', now())
                    ->exists();

            if($exists) {
                return redirect()->back()->with('error', 'Já existe uma declaração registada para este LNR!');
            }

            // Processar arquivo
            $nomeArquivo = null;
            if ($request->hasFile('ficheiro')) {
                $pathArquivo = $request->file('ficheiro')->store('documentos', 'public');
                $nomeArquivo = basename($pathArquivo);
            }

            $estado = EstadosModel::where('descricao_estado','Registado')->first();

            $declaracao = new TKxDeclaracaoModel;
            $declaracao->lnr = $request->input('lnr');
            $declaracao->saving = $request->input('saving');
            $declaracao->nome = mb_convert_case($request->input('nome'), MB_CASE_TITLE, 'UTF-8');
            $declaracao->documento = $request->input('documento');
            $declaracao->telefone = $request->input('telefone');
            $declaracao->banco_id = $request->input('banco');
            $declaracao->estado_id = $estado->id;
            $declaracao->ficheiro = $nomeArquivo;
            if($declaracao->save()) {
                return redirect()->back()->with('success', 'Declaração solicitada com sucesso!');
            }
            
            return redirect()->back()->with('error', 'Erro ao guardar declaração!');
    }

    public function recusarDeclaracao(Request $request)
    { 
        $validatedData = $request->validate([
            'comentario' => 'required|string|max:250',
            'id' => 'required|exists:tkxpedidodeclaracao,id'
        ]);

        $declaracao = TKxDeclaracaoModel::find($request->id);
        $estadoRecusado = EstadosModel::where('descricao_estado', 'Recusado')->first();

        $declaracao->estado_id = $estadoRecusado->id;
        $declaracao->comentario = $request->comentario;
        
        if($declaracao->save()) {
            return redirect()->back()->with('success', 'Declaração recusada com sucesso!');
        }
        return redirect()->back()->with('error', 'Erro ao recusar declaração!');
    }

    public function guardarReferenciaPagamento(TKxDeclaracaoModel $declaracao)
    {
        $authenticatedUser = Auth::user();
        
        try {
            
            dd($authenticatedUser->UtCodigo);
            
            // Verificar se a referência já existe
            $referenciaExistente = DB::table('referenciasmanuais')
                ->where('referencia', $request->txtRefPagamento)
                ->first();

            if ($referenciaExistente) {

                return redirect()->back()
                    ->with('error', 'Esta referência de pagamento já está em uso' . $referenciaExistente)
                    ->withInput();
            }
            
            $siglaagencia = TKxAgenciaModel::where('OfCodigo', $request->selectBase)->first();
            $loanNumber = $siglaagencia->OfIdentificador . '/' . $request->selectGrupoIndividual . '/' . $request->txtNumeroLoanSaving;


            $dados_activar_referencia = [
                "numero" => $request->txtRefPagamento,
                "validade" => Carbon::now()->addDays(3)->format('d/m/Y H:i'),
                "montante" => number_format($request->txtMontante, 2, ',', ' '),
                "cliente" => [
                    "nome" => $request->txtInfoAdicional,
                    "email" => "diversos@kxicredito.ao",
                    "telefone" => $request->telefone,
                ],
                "metadados" => [
                    "item1" => "Activação de referência de pagamento no ambiente prod.",
                    "item2" => "Manual",
                ],
            ];

            $client = new IziPayService();
            $response = $client->mainKxU($dados_activar_referencia);

            if ($response == 201) {               // Sucesso

                // Preparar os dados para inserção
                $dadosReferencia = [
                    'BuDadoOrigem' => $loanNumber,
                    'nomecliente' => $request->txtInfoAdicional,
                    'telefone' => $request->telefone,
                    'PoCodigo' => $request->selectProdutoSaving,
                    'tipo' => $request->selectGrupoIndividual,
                    'referencia' => $request->txtRefPagamento,
                    'inicio' => Carbon::now(),
                    'fim' => Carbon::now()->addDays(3),
                    'montante' => $request->txtMontante,
                    'idestado' => 21,
                    'BaseOperacao' => $siglaagencia->OfIdentificador,
                    'activo' => 1, // Mudado para 1 para indicar que está ativo
                    'UtCodigo' => $authenticatedUser->UtCodigo,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

                // Inserir na base de dados
                $id = DB::table('referenciasmanuais')->insertGetId($dadosReferencia);

                // Buscar dados completos para retorno
                $referenciaCompleta = DB::table('referenciasmanuais')
                    ->where('id', $id)
                    ->first();

                if ($dadosReferencia) {
                    $validKey = config('djanotifpgtref.callback_access_key');

                    $telefone = null;
                    $mensagem = "Pagamento KIXICREDITO\n\n" .
                        "Referência {$request->txtRefPagamento}\n" .
                        "Valor Kz " . number_format($request->txtMontante, 2, ',', '.') . "\n" .
                        "Cliente {$loanNumber}\n\n" .
                        "Validade 72 horas\n\n" .
                        "KIXICREDITO\n" .
                        "PARCEIRA NOS NEGÓCIOS";

                    $telefone = $request->telefone;


                    if ($telefone) {
                        $response = Http::withHeaders([
                            'Access-Key' => $validKey,
                            'Content-Type' => 'application/json',
                        ])->post('https://kixisms.kixicredito.com/api/enviarSMS', [
                                    'contacto' => $telefone,
                                    'mensagem' => $mensagem,
                                ]);


                    }
                    Log::info('Tentativa de envio SMS', ['telefone' => $telefone, 'mensagem ' => $mensagem, 'montante' => $request->txtMontante]);
                }


                return redirect()->route('referenciapgt')
                    ->with('success', 'Referência de pagamento guardada com sucesso!');


            } else if ($response == 422) {

                return back()->with('error', 'Referência ' . $request->numero . 'já existe.');

            } else if ($response == 201) {
                return back()->with('error', 'Lamentamos, O Serviços de Activação de referencia  Indisponível');
            }



        } catch (\Exception $e) {


            return redirect()->back()
                ->with('error', 'Erro ao processar referência de pagamento: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function aprovarDeclaracao($id)
    { 
        $declaracao = TKxDeclaracaoModel::find($id);
        $estadoAprovado = EstadosModel::where('descricao_estado', 'Aprovado')->first();
        
        // Verificar se a declaração existe
        if(!$declaracao) {
            return redirect()->back()->with('error', 'Declaração não encontrada!');
        }

        // Verificar se o estado de aprovação existe
        if(!$estadoAprovado) {
            return redirect()->back()->with('error', 'Estado de aprovação não encontrado!');
        }

        $this->guardarReferenciaPagamento($declaracao);

        
        
        dd($estadoAprovado);
        
        $declaracao->estado_id = $estadoAprovado->id;
        
        if($declaracao->save()) {
            return redirect()->back()->with('success', 'Declaração aprovada com sucesso!');
        }
         return redirect()->back()->with('error', 'Erro ao aprovar declaração!');  
    }
}
