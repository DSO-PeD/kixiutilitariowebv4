<?php

namespace App\Http\Controllers;



use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class RdReportEmailController extends Controller
{
    /** Remetente fixo: nunca vem do cliente. */
    private const REMETENTE = 'desenvolvimento@kixicredito.ao';
    private const REMETENTE_NOME = 'Kixi Crédito Angola';

    public function enviar(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'destinatario' => ['required', 'email:rfc', 'max:190'],
            'assunto'      => ['required', 'string', 'max:250'],
            'html'         => ['required', 'string', 'max:3000000'], // ~3 MB
            'direccao'     => ['nullable', 'string', 'max:10'],
        ]);

        $html = $this->sanitizarHtml($dados['html']);
        $assunto = trim(preg_replace('/[\r\n]+/', ' ', $dados['assunto']));

        try {
            Mail::html($html, function ($m) use ($dados, $assunto) {
                $m->from(self::REMETENTE, self::REMETENTE_NOME)
                  ->to($dados['destinatario'])
                  ->subject($assunto);
            });
        } catch (Throwable $e) {
            Log::error('Falha ao enviar RD Express por email', [
                'user'  => optional($request->user())->id,
                'para'  => $dados['destinatario'],
                'erro'  => $e->getMessage(),
            ]);

            return response()->json(
                ['message' => 'Não foi possível enviar o email. Tente novamente mais tarde.'],
                500
            );
        }

        Log::info('RD Express enviado por email', [
            'user'     => optional($request->user())->id,
            'para'     => $dados['destinatario'],
            'direccao' => $dados['direccao'] ?? null,
        ]);

        return response()->json(['message' => 'Email enviado com sucesso.']);
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
