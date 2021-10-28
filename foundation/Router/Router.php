<?php declare(strict_types = 1);

namespace Ben\Foundation\Router;

use Ben\Foundation\Exceptions\HttpException;
use Symfony\Component\HttpFoundation\Request;   // package HttpFoundation
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class Router
{
    protected RouteCollection $routes;
    protected RequestContext $context;   //  cette propriéte va nous permettre de configurer le contexte de la requete pour savoir quelle route on va devoir utiliser par rapport a la requete qu'on a recue
    protected Request $request;      // propriété Request qui va contenir des informations sur la requete qui etait faite et qui va nous permettre de la recuperer plus facilement on utilisant les SuperGlobals. ces information sur la requete je vais les recuperer grace au package HTTP Foundation
    protected array $params;
    protected string $controller;
    protected string $method;

    public function __construct(array $routes)
    {
        $this->initCSRF();                // utilisation de la methode initCSRF
        $this->provisionRoutes($routes);  // enregistrement des routes
        $this->makeRequestContext();       // appel a notre methode
        
        try {
            [$this->controller, $this->method] = $this->urlMatching();
        } catch (\Exception) {
            HttpException::render();
        }
    }

    protected function initCSRF(): void            // cette methode va nous permettre d'initialiser la verification du 'token' pour ne pas avoir la faille 'CSRF' sur notre site 
    {      // superGlobals Server va me permet de recuperer l'information en utilisant la clef 'Request'
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {        // on va faire cette verification, ca va etre de regarder la methode http qui est etait utiliser pour la requette, pour verifier si c'est la methode 'Post' ou non. 
            try {
                if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['_token']) {    // si le 'token' est transmis via le formulaire est different de celui de la session de mon utilisateur donc il y a un probleme du coup on lance une exception
                    throw new HttpException();           
                }
            } catch (HttpException) {          
                HttpException::render(403, 'Vous ne pouvez pas faire ça !');
            }
        }
    }

    protected function provisionRoutes(array $routes): void    // cette methode ne retournera a rien c'est pour ca on a mis (void)
    {
        $this->routes = new RouteCollection();   //  une fois qu on a notre instance de RouteCollection on va pouvoir utiliser sa methode add pour venir ajouter les differentes routes qu'on a creer dans Route.php
        foreach ($routes as $key => $route) {    // on ajouter toutes nos Routes grace a foreach
            $this->routes->add($key, $route);    // key -> index c'est le nom de la route sur notre fichier routes.php, $route -> instance de route symfony qui contient le nom index (key)
        }
    }

    protected function makeRequestContext(): void   // on utilise cette methode makerequestcontext pour gerer la requete qui vien d'arriver sur mon serveur  pour savoir quelle route on va devoir utiliser du coup vers quelle action on va devoir se diriger dans mon Controlleur et donc quelle methode on va utiliser 
    {
        $this->request = Request::createFromGlobals();  // cette methode statique va nous permettre de creer une nouvelle requete en prenant les informations qui sont contenue dans les différentes variable superglobals
        $this->context = new RequestContext();         // grace aux informations dans mes SuperGlobal je pouvoir venir fixer mon contexte 
        $this->context->fromRequest($this->request);    //  on peut fixer le comportement du 'context' on utilisant notre methode 'fromRequest' et on lui transmetant ce qui contient la propriete 'request'
        if (isset($_POST['_method']) && in_array(strtoupper($_POST['_method']), Route::HTTP_METHODS)) {
            $this->context->setMethod($_POST['_method']);
        }
    }

    protected function urlMatching(): array      // on a creer cette methode 'urlMatching' qui va s'occuper de trouver la bonne route par rapport a l'URI et la methode HTTP qui etait utiliser 

    {
        $matcher = new UrlMatcher($this->routes, $this->context);       // 'matcher' va s'occuper de trouver la route qui correspond dans ma colection de route et pour ce faire on a creer une nouvelle instance de la classe UrlMatcher 
        $this->params = $matcher->match($this->request->getPathInfo());  // pour recuperer avec mon Matcher les informations sur  la route qui a fonctionner dans ma liste de route (la route qui correspond a la requete qui vient d'etre faites) et bien j'ai utiliser la methode Match en lui indiquand en argument l'URI qui etais fourni par mon visiteur avec la methode getPathInfo

        return [$this->params['_controller'], $this->params['_method']];
    }

    public function getInstance(): void   
    {
        $this->cleanParams();   
        call_user_func_array([new $this->controller(), $this->method], $this->params);  //  transmettre les parametres qui sont nettoye
    }

    protected function cleanParams(): void   
    {
        foreach ($this->params as $key => $param) {
            if (str_starts_with($key, '_')) {         
                unset($this->params[$key]);
            }
        }
    }

    public function getGenerator(): UrlGenerator
    {
        return new UrlGenerator($this->routes, $this->context);  
    }

    public static function get(string $name, array $data = []): string  // avec cette methode on peut utiliser ma classe router n'importe ou
    {
        $generator = $GLOBALS['app']->getGenerator();    
        $uri = $generator->generate($name, $data);  // pour generer notre 'uri' on utilise la methode 'generate' cette methode va prendre le nom de la route et les donnees pour les parametre variable de la route en question
        return $uri;
    }
}