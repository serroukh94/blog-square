<?php declare(strict_types = 1);

use App\Controllers\AuthController;
use App\Controllers\BaseController;
use App\Controllers\HomeController;
use App\Controllers\PostController;
use Ben\Foundation\Router\Route;

return [ 
       // la on a creer une route index car ca sera la page d'accueil, si jamais on a une requette avec la methodes http GET  sur la racine ('/') de notre site web et bien dans ce cas la on veut venir excecuter la methode index qui se trouve dans le controlleur BaseController
    
           // Authentification
    'register.form'    => Route::get('/inscription', [AuthController::class, 'registerForm']),  // cette route utilise la methode httpGet pour acceder au
    'register.request' => Route::post('/inscription', [AuthController::class, 'register']), // la route de soumission du formulaire 
    'login.form'       => Route::get('/connexion', [AuthController::class, 'loginForm']),
    'login.request'    => Route::post('/connexion', [AuthController::class, 'login']),
    'logout'           => Route::post('/deconnexion', [AuthController::class, 'logout']),
    
           
           // Espace membre
    'home'               => Route::get('/compte', [HomeController::class, 'index']),  // cette route s'appele 'home' ca sera une route qui utilise la methode httpGet avec URI /compte et ca va se passe dans le HomeController, et la methode ca sera index puisque c la page d'accueil de mon espace membre 
    'home.updateName'    => Route::patch('/compte', [HomeController::class, 'updateName']),  //  // on a utiliser la methode 'patch' pour mettre a jour partiellement une ressource, puisque ici on va mettre partiellement a jour notre entré dans la table user
    'home.updateEmail'   => Route::patch('/compte/email', [HomeController::class, 'updateEmail']),  //  // on a utiliser la methode 'patch' pour mettre a jour partiellement une ressource, puisque ici on va mettre partiellement a jour notre entré dans la table user
    'home.updatePassword'=> Route::patch('/compte/password', [HomeController::class, 'updatePassword']),

           
           // blog
     'index' => Route::get('/', [PostController::class, 'index']),
     'index.form' => Route::get('/form', [PostController::class, 'form']),
     'posts.create' => Route::get('/posts/creer', [PostController::class, 'create']),  
     'posts.store' => Route::post('/posts/creer', [PostController::class, 'store']) ,      
     'posts.show' => Route::get('/posts/{slug}', [PostController::class, 'show']),      
     'posts.comment' => Route::post('/posts/{slug}', [PostController::class, 'comment']),      
     'posts.delete' => Route::delete('/posts/{slug}', [PostController::class, 'delete']),      
     'posts.edit' => Route::get('/posts/{slug}/modifier', [PostController::class, 'edit']),      
     'posts.update' => Route::patch('/posts/{slug}/modifier', [PostController::class, 'update'])      
     
     
];