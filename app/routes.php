<?php declare(strict_types = 1);

use App\Controllers\BaseController;  //les entites
use Ben\Foundation\Router\Route;

return [ 
    'index' => Route::get('/', [BaseController::class, 'index']),   // la on a creer une route index car ca sera la page d'accueil, si jamais on a une requette avec la methodes http GET  sur la racine ('/') de notre site web et bien dans ce cas la on veut venir excecuter la methode index qui se trouve dans le controlleur BaseController
];