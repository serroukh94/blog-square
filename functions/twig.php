<?php declare(stricts_types = 1);

// on va d'abord creer toutes les fontions qu'on voudra rendre disponible au niveau de mes templates pour pouvoir generer mes vue html et ensuite on va les ajouter en second temp a twig pour l'indiquer du coup que tu peut utiliser ces differents fonctions 

use Ben\Foundation\Authentication;
use Ben\Foundation\Router\Router;
use Ben\Foundation\Session;
use Ben\Foundation\View;

if (!function_exists('auth')) {       // on va tester si la fonction n'existe pas comme ca on pourra la creer si non on garde que celle qui existe avant.
    function auth(): Authentication
    {
        return new Authentication();
    }
}

if (!function_exists('route')) {
    function route(string $name, array $data = []): string
    {
        return Router::get($name, $data);
    }
}

if (!function_exists('errors')) {
    function errors(?string $field = null): ?array
    {
        $errors = Session::getFlash(Session::ERRORS);
        if ($field) {
            return $errors[$field] ?? null;
        }
        return $errors;
    }
}

if (!function_exists('status')) {          // messages  d'information et de validation 
    function status(): ?string
    {
        return Session::getFlash(Session::STATUS);
    }
}

if (!function_exists('csrf_field')) {       
    function csrf_field(): string
    {
        return View::csrfField();  
    }
}     

if (!function_exists('method')) {
    function method(string $httpMethod): string
    {
        return View::method($httpMethod);
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = null): mixed
    {
        return View::old($key, $default);  // donc on passe la clef et la valeur par default
    }
}