<?php declare(strict_types = 1);     

namespace Ben\Foundation\Exceptions;

use Ben\Foundation\View;

class HttpException extends \Exception            // pour afficher nos pages d'erreurs http avec 'render'
 
{   // cette methode c'est pour venir generer une page d'erreur en fonction du code d'erreur et du message que je souhaite afficher c'est pour cela on a creer la methode suivante
    public static function render(int $httpCode = 404, string $message = 'Page non trouvée'): void
    {
        http_response_code($httpCode);        // au niveau de ce contenue on a fait deux choses : on a fixer d'abbord le code  Http de la reponse qui va etre renvoyer a mon utilisateur du coup on utiliser '$httpCode' 
        View::render('errors.default', [      // ensuite on viendra rendre notre vue ca sera pour l'instant un simple message 
            'httpCode' => $httpCode,
            'message' => $message,
        ]);
        die;                     //  enfin on a fait 'die' pour arreter l'execution de script et du coup retourner ce qu'on aura indiquer en vue a notre visiteur 
    }
}