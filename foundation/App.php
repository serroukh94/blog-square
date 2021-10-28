<?php declare(strict_types = 1);

namespace Ben\Foundation;

use Illuminate\Database\Capsule\Manager as Capsule;
use Ben\Foundation\Exceptions\HttpException;
use Ben\Foundation\Router\Router;
use Symfony\Component\Routing\Generator\UrlGenerator;

class App
{
    protected Router $router;

    public function __construct()
    // initialisation des composants (BDD, routes, sessions, php dotenv...)
    {
        $this->initDotenv();
        if (Config::get('app.env') === 'production') {   
            $this->initProductionExceptionHandler();
        }
        $this->initSession();
        $this->initDatabase();
        $this->router = new Router(require ROOT.'/app/routes.php');
    }

    protected function initDotenv(): void  
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(ROOT);  // pour initialiser php dotenv avec la methode createImmutable ensuite on a indiquer la racine de notre projet ROOT ou se trouve le fichier .env (composant pour plus d'information rendez-vous sur packagist)
        $dotenv->safeLoad();             // la methode safeload va nous permettre de ne pas avoir d'erreur dans le cas ou il n'y aura pas de fichier .env  a lire  (exemple en cas de versionning sut github)
    }

    protected function initProductionExceptionHandler(): void      // on a initialiser le gestionnaire d'erreurs pour la production 
    {
        set_exception_handler(                   // et pour faire ceci on a utiliser cette fonction 
            fn () => HttpException::render(500, 'Houston, on a un problème! 🚀')    // et donc ce qu'on fera ici, si l'erreur n'a pas etait gerer donc une erreur 500 qui doit etre tourner car y'a eu un probleme dans mes script que je n'ai pas gerer 
        );
    }

    protected function initSession(): void  
    {
        Session::init();
        Session::add('_token', Session::get('_token') ?? $this->generateCsrfToken());    
    }

    protected function generateCsrfToken(): string
    {
        $length = Config::get('hashing.csrf_token_length');     //  nombre d'octe que je veux recuperer avec la classe config
        $token = bin2hex(random_bytes($length));              // recuperation de 'token'
        return $token;                                      // une fois qu'on a generer notre 'token' on peut le retourner 
    }

    protected function initDatabase(): void           // on va initialiser notre gestion de BDD 

    {   //on va commencer par faire pour pouvoir utiliser notre dependance 'illuminate/database' pour venir ensuite generer nos BDD avec l'ORM eloquent et bien ca va etre de creer une nouvelle instance de la classe manager alliacé avec le nom capsule 

        date_default_timezone_set(config::get('app.timezone'));  
        $capsule = new Capsule();                             //  j'ai fait la meme chose que sur la documentation 
        $capsule->addConnection([                             // ici on a configurer notre conexion a notre SGBD et indiquer quelle BDD on veut utiliser avec la methode 'addConnection'
            'driver'   => Config::get('database.driver'),
            'host'     => Config::get('database.host'),
            'database' => Config::get('database.name'),
            'username' => Config::get('database.username'),
            'password' => Config::get('database.password'),
        ]);
        $capsule->setAsGlobal();         // avec la methode 'setAsGlobal' on va indiquer que l'instance capsule que j'ai creer et avec la configuration je peux l'acceder depuis n'impote quelle endroit dans mes scripts 
        $capsule->bootEloquent();       //  avec la methode 'bootEloquent' on va demarer L'ORM Eloquent pour pouvoir l'utiliser.
    }

    public function render(): void     // on utilisera la methode render qui nous permettre de rendre notre reponse et du coup ensuite de la transmettre a notre utilisateur 
    {
        $this->router->getInstance();  // on a utiliser le router avec la methode 'getInstace' c'est pour venir indiquer avec la methode 'render' qu'on souhaite executer les instruction qui sont dans la methode de mon controlleur 
        Session::resetFlash();
    }

    public function getGenerator(): UrlGenerator
    {
        return $this->router->getGenerator();
    }
}