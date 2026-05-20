<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherHelper extends Model
{
    const BASE62 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    const BANCO_MAP = ['BMA' => 0];

    const BASE_MAP = [
        'HO' =>	1,
        'HU' =>	2,
        'MB' =>	3,
        'HH' =>	4,
        'SP' =>	5,
        'VI' =>	6,
        'KK' =>	7,
        'BE' =>	8,
        'CA' =>	9,
        'LB' =>	10,
        'NB' =>	11,
        'KT' =>	12,
        'UE' =>	13,
        'SO' =>	14,
        'DD' =>	15,
        'MG' =>	16,
        'SM' =>	17,
        'SB' =>	18,
        'KX' =>	19,
        'RN' =>	20,
        'RS' =>	21,
        'BA' =>	22,
        'ME' =>	23,
        'MO' =>	24,
        'MC' =>	25,
        'PE' =>	26,
        'AC' =>	27,
        'DP' =>	28
    ];

    const RADIX = [3, 32, 13, 9, 28, 100000, 24, 60, 60];

    // Inversão de mapeamento
    private static $BANCO_MAP_INV = null;
    private static $BASE_MAP_INV = null;

    private static function initInverseMaps()
    {
        if (!self::$BANCO_MAP_INV) {
            self::$BANCO_MAP_INV = array_flip(self::BANCO_MAP);
            self::$BASE_MAP_INV  = array_flip(self::BASE_MAP);
        }
    }

    /** BASE62 */
    public static function toBase62(int $n): string
    {
        if ($n === 0) return self::BASE62[0];
        $s = '';
        while ($n > 0) {
            $r = $n % 62;
            $n = intdiv($n, 62);
            $s = self::BASE62[$r] . $s;
        }
        return $s;
    }

    public static function fromBase62(string $s): int
    {
        $n = 0;
        $chars = str_split($s);
        foreach ($chars as $c) {
            $n = $n * 62 + strpos(self::BASE62, $c);
        }
        return $n;
    }

    /** Construção do Voucher por parte de 9 */
    public static function parseVoucher(string $texto): array
    { 
        if (strlen($texto) < 23) {
            throw new \Exception("Formato de voucher inválido");
        }
    
        return [
            'banco'   => substr($texto, 0, 3),
            'dia'     => (int)substr($texto, 3, 2),
            'mes'     => (int)substr($texto, 5, 2),
            'ano'     => (int)substr($texto, 7, 4),
            'base'    => substr($texto, 11, 2),
            'credito' => (int)substr($texto, 13, 5),
            'hora'    => (int)substr($texto, 18, 2),
            'minuto'  => (int)substr($texto, 20, 2),
            'segundo' => (int)substr($texto, 22, 2),
        ];
    }

    public static function montarVoucher(array $v): string
    {
        return sprintf(
            '%s%02d%02d%d%s%05d%02d%02d%02d',
            $v['banco'],
            $v['dia'],
            $v['mes'],
            $v['ano'],
            $v['base'],
            $v['credito'],
            $v['hora'],
            $v['minuto'],
            $v['segundo']
        );
    }

    public static function criptografar(array $v): string
    { 
        self::initInverseMaps();

        $valores = [
            self::BANCO_MAP[$v['banco']],
            $v['dia'],
            $v['mes'],
            $v['ano'] - 2024,
            self::BASE_MAP[$v['base']],
            $v['credito'],
            $v['hora'],
            $v['minuto'],
            $v['segundo']
        ]; 

        $n = 0;
        foreach ($valores as $i => $valor) {
            $n = $n * self::RADIX[$i] + $valor;
        }
       
        return str_pad(self::toBase62($n), 9, '0', STR_PAD_LEFT);
    }

    public static function descriptografar(string $codigo): array
    {
        self::initInverseMaps();

        $n = self::fromBase62($codigo);
        $valores = [];

        foreach (array_reverse(self::RADIX) as $base) {
            $r = $n % $base;
            $n = intdiv($n, $base);
            array_unshift($valores, $r);
        }
 
        return [
            'banco'   => self::$BANCO_MAP_INV[$valores[0]],
            'dia'     => $valores[1],
            'mes'     => $valores[2],
            'ano'     => $valores[3] + 2024,
            'base'    => self::$BASE_MAP_INV[$valores[4]],
            'credito' => $valores[5],
            'hora'    => $valores[6],
            'minuto'  => $valores[7],
            'segundo' => $valores[8],
        ];
    }
}
