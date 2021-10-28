<?php declare(strict_types = 1);

return [
    'template_extension' => 'html',
    'functions' => [                        // la clef function contient un tableau de differentes fonctions qu'on va souhaiter ajouter a twig 
        'auth', 'route', 'errors', 'status',
        'csrf_field', 'method', 'old',
    ],
];