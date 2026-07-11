<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\TKxBancoModel;
use App\Models\EstadosModel;
use App\Models\TKxDeclaracaoModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\IziPayService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\HelperModel;

class DeclaracaoController extends Controller
{

    protected $authenticatedUser;
    private $montante = 11000; //Valor fixo para comparação de pagamento

    public function __construct()
    {
        $this->authenticatedUser = Auth::user();
    }

    public function viewDeclaracoes(Request $request)
    {
        $lista_bancos = TKxBancoModel::getBancos();
        $lista_estados = EstadosModel::getEstadosDeclaracao();

        $query = DB::table('tkxpedidodeclaracao as decl')
                        ->join('tkxclbanco as banc', 'decl.banco_id', '=', 'banc.BaCodigo')
                        ->join('estado as est', 'decl.estado_id', '=', 'est.id')
                        ->leftjoin('referenciasmanuais as ref','ref.referencia','=','decl.referencia')
                        ->select(
                            'decl.*',
                            'banc.BaNome', 
                            'est.descricao_estado', 
                            'est.color',
                            'ref.inicio',
                            'ref.fim',
                            'ref.montante',
                            'ref.montantepago',
                            'ref.activo'
                        );
        
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
                            ->paginate(50)
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
                        ->leftjoin('referenciasmanuais as ref','ref.referencia','=','decl.referencia')
                        ->select(
                            'decl.*', 
                            'banc.BaNome', 
                            'est.descricao_estado',
                            'est.color',
                            'ref.inicio',
                            'ref.fim',
                            'ref.montante',
                            'ref.montantepago',
                            'ref.activo'
                        )
                        ->where('decl.id', $id)
                        ->first();
        if (!$declaracao) {
            return redirect()->back()->with('error', 'Declaração não encontrada!');
        }

        $fileUrl = $declaracao->ficheiro 
                    ? asset('storage/documentos/' . $declaracao->ficheiro)
                    : null;

        $declaracao->ficheiro = $fileUrl;

        //Ver se já tem pagamento
        $limit = $this->montante; 

        $declaracao->isPago = $declaracao->montantepago >= $limit ? true:false;   

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
            $declaracao->criadoPor = HelperModel::splitName($this->authenticatedUser->UtNome);

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

        if($declaracao->referencia){        
            return redirect()->back()->with('error', 'A Declaração já foi aprovada anteriormente.');
        }

        $declaracao->estado_id = $estadoRecusado->id;
        $declaracao->comentario = $request->comentario;
        $declaracao->recusadoPor = HelperModel::splitName($this->authenticatedUser->UtNome);
        
        if($declaracao->save()) {
            return redirect()->back()->with('success', 'Declaração recusada com sucesso!');
        }
        return redirect()->back()->with('error', 'Erro ao recusar declaração!');
    }

    //Gerar referência de pagamento randomico e base AC = DPP 
    public function gerarReferenciaPagamento()
    {
        $numero = rand(10000, 99999);
        $referencia = '9973' . $numero; // Concatenar o prefixo com o número aleatório
        return $referencia;
    }

    public function mensagemNotifica(TKxDeclaracaoModel $declaracao, $referencia, $montante)
    {
        $validKey = config('djanotifpgtref.callback_access_key');

        $mensagem = "Pedido de DECLARAÇÃO n.º {$declaracao->lnr} APROVADO\n\n" .
                    "REFERÊNCIA DE PAGAMENTO\n" .
                    "Entidade: 00589\n" .
                    "Referência: {$referencia}\n" .
                    "Valor: " . number_format($this->montante, 2, ',', '.') . " AKZ\n" .
                    "Valido até: " . Carbon::now()->addDays(1)->format('d/m/Y H:i') . "\n";

        if ($declaracao->telefone) {
            $response = Http::withoutVerifying()->withHeaders([
                'Access-Key' => $validKey,
                'Content-Type' => 'application/json',
            ])->post('https://kixisms.kixicredito.com/api/enviarSMS', [
                'contacto' => $declaracao->telefone,
                'mensagem' => $mensagem,
            ]);
        }
        Log::info('Tentativa de envio SMS', ['telefone' => $declaracao->telefone, 'mensagem ' => $mensagem, 'montante' => $this->montante]);
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
            
        if($declaracao->referencia){        
            return redirect()->back()->with('error', 'Declaração já aprovada anteriormente!');
        }

        /** Inicio do processo de geração de referência de pagamento */
        do {
            //Gerar referência de pagamento            
            $referencia = $this->gerarReferenciaPagamento();
        } while (
            DB::table('referenciasmanuais')
                ->where('referencia', $referencia)
                ->exists()
        ); 
            
        $dados_activar_referencia = [
                "numero" => $referencia,
                "validade" => Carbon::now()->addDays(3)->format('d/m/Y H:i'),
                "montante" => number_format($this->montante, 2, ',', ' '),
                "cliente" => [
                    "nome" => $declaracao->nome,
                    "email" => "diversos@kxicredito.ao",
                    "telefone" => $declaracao->telefone,
                ],
                "metadados" => [
                    "item1" => "Activação de referência de pagamento no ambiente prod.",
                    "item2" => "Manual",
                ],
        ];

        $client = new IziPayService();
        $response = $client->mainKxU($dados_activar_referencia);

        $sucesso1 = false;
        $sucesso2 = false;

        if ($response == 201) {              

            // Preparar os dados para inserção
            DB::beginTransaction();

                $dadosReferencia = [
                    'BuDadoOrigem' => $declaracao->saving,
                    'nomecliente' => $declaracao->nome,
                    'telefone' => $declaracao->telefone,
                    'PoCodigo' => 'S12',
                    'tipo' => 'I',
                    'referencia' => $referencia,
                    'inicio' => Carbon::now(),
                    'fim' => Carbon::now()->addDays(1),
                    'montante' => $this->montante,
                    'idestado' => 21,
                    'BaseOperacao' => substr($declaracao->lnr, 0, 2),
                    'activo' => 1, // Mudado para 1 para indicar que está ativo
                    'UtCodigo' => Auth::user()->UtCodigo,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

            // Inserir na base de dados
            $id = DB::table('referenciasmanuais')->insertGetId($dadosReferencia);

            $sucesso1 = $id > 0 ? true : false;

            // Buscar dados completos para retorno
            $referenciaCompleta = DB::table('referenciasmanuais')
                                    ->where('id', $id)
                                    ->exists();
            
            $declaracao->estado_id = $estadoAprovado->id;
            $declaracao->comentario = 'Declaração aprovada com referência de pagamento gerada: ' . $referencia;  
            $declaracao->referencia = $referencia;
            $declaracao->aprovadoPor = HelperModel::splitName($this->authenticatedUser->UtNome);

            if($declaracao->save()) {
                $sucesso2 = true;
            }
        }
        
        if($sucesso1 && $sucesso2) {
            DB::commit(); echo 'AQUI';
            $this->mensagemNotifica($declaracao, $referencia, $this->montante);
            
            return redirect()->back()->with('success', 'Declaração aprovada com sucesso!');
        }

        DB::rollBack();
        return redirect()->back()->with('error', 'Erro ao aprovar declaração!');  
    }

    public function imprimirDeclaracao($id)
    {
        $declaracao = DB::table('tkxpedidodeclaracao as decl')
                        ->join('tkxclbanco as banc', 'decl.banco_id', '=', 'banc.BaCodigo')
                        ->join('estado as est', 'decl.estado_id', '=', 'est.id')
                        ->leftjoin('referenciasmanuais as ref','ref.referencia','=','decl.referencia')
                        ->select(
                            'decl.*',
                            'banc.BaSigla',
                            'banc.BaNome',
                            'est.descricao_estado',
                            'est.color',
                            'ref.montantepago'
                        )
                        ->where('decl.id', $id)
                        ->first(); 

        if (!$declaracao) {
            return redirect()->back()->with('error', 'Declaração não encontrada!');
        }

        /** Verificar se existe pagamento desta declaração */
        $limit = $this->montante;
        
        if($declaracao->montantepago < $limit){
            return redirect()->back()->with('error', 'Nenhum pagamento encontrado.');  
        }

        $declaracao->data_aprovacao = Carbon::parse($declaracao->updated_at)->translatedFormat('d \d\e F \d\e Y');
        $declaracao->BaNome = mb_strtoupper($declaracao->BaNome, 'UTF-8');
        $declaracao->nome = mb_strtoupper($declaracao->nome, 'UTF-8');
        $declaracao->documento = $declaracao->documento; 
    
        $key = strrev($declaracao->lnr).'/'.date('dmYHis',strtotime($declaracao->updated_at)).'/'.$declaracao->id;

        $qr = base64_encode(
            QrCode::format('svg')
                ->size(70)
                ->errorCorrection('H')
                ->generate($key)
        );

        $data = [
            'declaracao' => $declaracao,
            'qr' => $qr
        ];

        $pdf = PDF::loadView('reports.reportDeclaracao',$data)->setOption(['dpi' => 100, 'defaultFont' => 'sans-serif']);

        return $pdf->stream();
    }
}
