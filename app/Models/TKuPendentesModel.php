<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TKuPendentesModel extends Model
{
    //

    use HasFactory;

    protected $table = 'tkupendentes';

    protected $fillable = [
        'id',
        'CiFecha',
        'Lnr',
        'Tipo',
        'LPF',
        'KxU',
        'voucher',
        'montante',
        'budata',
        'BaseOperacao'
    ];
}
