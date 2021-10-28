<?php declare(strict_types = 1);

return [                                     // chaque fichier de configuration retourne un tableau, ce tableau va etre stocker dans ma variable config dans la method get
    'env' => env('APP_ENV', 'production'),  // on a mis par exemple la clef 'env' et du coup on a mis en valeur ma variable 'environnement' et donc a defaut on a mis 'production'
    'timezone' => 'Europe/Paris',  

];