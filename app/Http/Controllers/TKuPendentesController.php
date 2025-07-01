<?php

namespace App\Http\Controllers;

use App\Models\TKuPendentesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TKuPendentesController extends Controller
{
    //

    public function carregaPendentesKP(Request $request)
    { // php artisan tinker → Str::random(32)

        $tokenRecebido = "4c947f37-23bdja-4717-aa2d-df618c8175bf";//$request->header('X-APP-TOKEN');
        $tokenEsperado = env('APP_SECRET_TOKEN');

        if (!$tokenRecebido || $tokenRecebido !== $tokenEsperado) {

            return response()->json(['error' => 'Acesso Não autorizado'], 401);
        }

        // Validar os dados recebidos
        /*   $validator = Validator::make($request->all(), [
               '*.CiFecha' => 'required|date',
               '*.Lnr' => 'required|string|max:20',
               '*.Tipo' => 'required|string|max:1',
               '*.Lpf' => 'required|integer',
               '*.KxU' => 'required|integer',
               '*.voucher' => 'nullable|string|max:50',
               '*.montante' => 'required|numeric',
               '*.budata' => 'required|date',
               '*.BaseOperacao' => 'required|string|max:10',
           ]);

           if ($validator->fails()) {
               return response()->json([
                   'success' => false,
                   'errors' => $validator->errors()
               ], 422);
           }
*/
        try {
            $registros = [];

            TKuPendentesModel::truncate();

            foreach ($request->all() as $item) {
                $dataFormatadaCiFecha = Carbon::parse($item['CiFecha'])->format('Y-m-d');
                $dataFormatadaBuData = Carbon::parse($item['budata'])->format('Y-m-d');

                $registros[] = TKuPendentesModel::create([
                    'CiFecha' => $dataFormatadaCiFecha,
                    'Lnr' => $item['lnr'],
                    'Tipo' => $item['tipo'],
                    'LPF' => $item['lpf'],
                    'KxU' => $item['kxu'],
                    'voucher' => $item['voucher'] ?? 'R/S voucher',
                    'montante' => $item['montante'],
                    'budata' => $dataFormatadaBuData,
                    'BaseOperacao' => $item['baseOperacao'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Registros criados com sucesso',
                'data' => $registros
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar registros',
                'error' => $e->getMessage()
            ], 500);
        }

    }

}
