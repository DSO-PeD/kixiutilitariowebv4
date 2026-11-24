<?php

// Guardar como config/enviar_rd.php
// Depois de alterar o .env: php artisan config:clear (ou config:cache em produção)

return [
    'grupos' => [
        'CDO' => env('EMAIL_CDO'),
        'DPP' => env('EMAIL_DPP'),
        'DOP' => env('EMAIL_DOP'),
        'OUT' => env('EMAIL_OUT'),
    ],
];

/*
 .env — vários membros separados por vírgula:

 EMAIL_CDO=comite.desempenho@kixicredito.ao
 EMAIL_DPP=tirso.figueira@kixicredito.ao,outro.membro@kixicredito.ao
 EMAIL_DOP=calunga.fortunato@kixicredito.ao,outro.membro@kixicredito.ao
 EMAIL_OUT=marta.santos@kixicredito.ao
*/
