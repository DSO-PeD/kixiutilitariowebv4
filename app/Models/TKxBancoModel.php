<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TKxBancoModel extends Model
{
    protected $table = 'tkxclbanco';
    public static function getBancos(){

        return TKxBancoModel::all();
    }
}
