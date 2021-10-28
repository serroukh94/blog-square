<?php declare(strict_types = 1);

return [              // on va renseigner les differentes valeur pour nous connecter a notre SGBD et utiliser la bonne BDD
    'driver'   => env('DB_DRIVER', 'mysql'),   // on a utiliser la variable d'environemment DB_DRIVER si jamais n'existe pas ca sera mysql par default
    'host'     => env('DB_HOST', 'localhost'),
    'name'     => env('DB_DATABASE', 'forge'),   //  on va deployer notre site avec un processus moderne de deploiment et pour centraliser tous ca en utilisera un outil qui s'apelle laravel forge qui va nous permettre de gerer nos serveur  pour pouvoir avoir nos sites en ligne, et du coup par defaut on aura tendance de mettre comme nom de BDD 'forge' et nom d'utilisateur 'forge'
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
];


// avec laravel forge le nom d'utilisateur et le nom de BDD par default ca sera 'forge' 

// on va renseigner les differentes valeur sur notre fichier .env