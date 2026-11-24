<?php

namespace App\Http\Controllers;



use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Throwable;

class RdReportEmailController extends Controller
{
    /** Remetente fixo: nunca vem do cliente. */
    private const REMETENTE = 'desenvolvimento@kixicredito.ao';
    private const REMETENTE_NOME = 'Kixi Crédito Angola';
    /**
     * Grupo de distribuição => direcção do relatório que lhe pode ser enviado.
     * Impede, por exemplo, enviar o HTML global ao grupo DOP.
     */
    private const GRUPOS = [
        'CDO' => 'GLOBAL',
        'DOP' => 'DOP',
        'DPP' => 'DPP',
        'OUT' => 'OUT',
    ];

    public function enviar(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'grupo' => ['nullable', 'string', Rule::in(array_keys(self::GRUPOS))],
            // obrigatório apenas quando não há grupo (opção "Nenhum" no diálogo)
            'destinatario' => ['nullable', 'required_without:grupo', 'email:rfc', 'max:190'],
            'assunto' => ['required', 'string', 'max:250'],
            'html' => ['required', 'string', 'max:3000000'], // ~3 MB
            'direccao' => ['nullable', 'string', 'max:10'],
        ]);

        $grupo = $dados['grupo'] ?? null;

        // O grupo só pode receber o relatório da sua própria direcção
        if ($grupo !== null) {
            $direccaoEnviada = strtoupper(trim((string) ($dados['direccao'] ?? '')));

            if ($direccaoEnviada !== self::GRUPOS[$grupo]) {
                return response()->json(
                    ['message' => "O grupo {$grupo} não pode receber o relatório da direcção {$direccaoEnviada}."],
                    422
                );
            }
        }

        $destinatarios = $grupo !== null
            ? $this->destinatariosDoGrupo($grupo)
            : [$dados['destinatario']];

        if (empty($destinatarios)) {
            Log::error('Grupo de email sem destinatários configurados', [
                'user' => optional($request->user())->id,
                'grupo' => $grupo,
            ]);

            return response()->json(
                ['message' => "O grupo {$grupo} não tem destinatários configurados."],
                500
            );
        }

        $html = $this->sanitizarHtml($dados['html']);
        $assunto = trim(preg_replace('/[\r\n]+/', ' ', $dados['assunto']));

        try {
            // O remetente é sempre o configurado no servidor (o valor enviado pelo browser é ignorado)
            Mail::html($html, function ($m) use ($destinatarios, $assunto) {
                $m->from(self::REMETENTE, self::REMETENTE_NOME)
                    ->to($destinatarios)
                    ->subject($assunto);
            });
        } catch (Throwable $e) {
            Log::error('Falha ao enviar RD Express por email', [
                'user' => optional($request->user())->id,
                'grupo' => $grupo,
                'para' => implode(', ', $destinatarios),
                'erro' => $e->getMessage(),
            ]);

            return response()->json(
                ['message' => 'Não foi possível enviar o email. Tente novamente mais tarde.'],
                500
            );
        }

        Log::info('RD Express enviado por email', [
            'user' => optional($request->user())->id,
            'grupo' => $grupo,
            'para' => implode(', ', $destinatarios),
            'direccao' => $dados['direccao'] ?? null,
        ]);

        return response()->json([
            'message' => $grupo !== null
                ? "Email enviado ao grupo {$grupo}."
                : 'Email enviado com sucesso.',
        ]);
    }

    /**
     * Lê os emails do grupo a partir de config/enviar_rd.php (alimentado pelo .env).
     * Aceita vários endereços separados por vírgula ou ponto e vírgula.
     */
    private function destinatariosDoGrupo(string $grupo): array
    {
        $bruto = (string) config("enviar_rd.grupos.{$grupo}", '');

        $emails = array_filter(
            array_map('trim', preg_split('/[;,]/', $bruto) ?: []),
            fn($e) => $e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL)
        );

        return array_values(array_unique($emails));
    }
    /** Remove conteúdo activo: o email só deve levar HTML/CSS estático. */
    private function sanitizarHtml(string $html): string
    {
        $html = preg_replace('#<(script|iframe|object|embed|form)\b[^>]*>.*?</\1>#is', '', $html);
        $html = preg_replace('#<(script|iframe|object|embed|form|link)\b[^>]*/?>#is', '', $html);
        $html = preg_replace('#\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html);
        $html = preg_replace('#javascript:#i', '', $html);

        return $html;
    }
}
