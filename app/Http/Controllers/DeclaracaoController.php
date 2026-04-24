<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\TKxBancoModel;
use App\Models\EstadosModel;
use App\Models\TKxDeclaracaoModel;
use Illuminate\Support\Facades\DB;

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
}
