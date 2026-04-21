<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DeclaracaoController extends Controller
{
    public function viewDeclaracoes()
    {
        return Inertia::render('Declaracoes', [
            'declaracoes' => [],         
        ]);
    }
}
