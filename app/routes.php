<?php declare(strict_types = 1);

use App\Controllers\AuthController;
use App\Controllers\BaseController;  //les entites
use App\Controllers\HomeController;
use Ben\Foundation\Router\Route;

return [ 
    'index' => Route::get('/', [BaseController::class, 'index']),   // la on a creer une route index car ca sera la page d'accueil, si jamais on a une requette avec la methodes http GET  sur la racine ('/') de notre site web et bien dans ce cas la on veut venir excecuter la methode index qui se trouve dans le controlleur BaseController
    
           // Authentification
    'register.form' => Route::get('/inscription', [AuthController::class, 'registerForm']),
    'register.request' => Route::post('/inscription', [AuthController::class, 'register']), // la route de soumission du formulaire 
           
           // Espace membre
    'home' => Route::get('/compte', [HomeController::class, 'index']),  // cette route s'appele home ca sera une route qui utilise la methode httpGet avec URI /compte et ca va se passe dans le HomeController, et la methode ca sera index puisque c la page d'accueil de mon espace membre 
     


];