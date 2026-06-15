<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelperModel extends Model
{
    public static function splitName($fullName)
    {
        $nameParts = explode(' ', $fullName);
        $firstName = array_shift($nameParts);
        $lastName = array_pop($nameParts);

        return $firstName. ' ' . $lastName;
    }
}
