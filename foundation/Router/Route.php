<?php declare(strict_types = 1);

namespace Ben\Foundation\Router;    

use Ben\Foundation\AbstractController;
use Symfony\Component\Routing\Route as SymfonyRoute;

class Route   // on va indiquer a notre classe route toutes les methodes http qu'on souhaite autoriser pour notre site web
{
    public const HTTP_METHODS = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'];   //  on va renseigner dans un 1er temp la constante HTTP_METHODS  (Patch c'est pour mettre a jour)

    public static function __callStatic(string $httpMethod, array $arguments): SymfonyRoute   
    {
        if (!in_array(strtoupper($httpMethod), static::HTTP_METHODS)) {     //  on a utiliser la fonction in_array  pour verifier si une valeur est bel et bien  dans un tableau ou non si il y est ca nous retourne boolean true si il n'y est pas ca nous retourne false
            throw new \BadMethodCallException(
                sprintf('Méthode HTTP indisponible (%s)', $httpMethod)
            );
        }
        [$uri, $action] = $arguments;                    // a partir de ces deux variable uri,action  et egalement de la methode de HTTP on va pouvoir fabrique notre route symfony 
        return static::make($uri, $action, $httpMethod);
    }

    protected static function make(string $uri, array $action, string $httpMethod): SymfonyRoute
    {
        [$controller, $method] = $action;      
        if (!static::checkIfActionExists($controller, $method)) {    
            throw new \InvalidArgumentException(
                sprintf('L\'action n\'existe pas (%s)', implode(', ', $action))   // la methode implode nous permet d'indiquer avec quoi je souhaite fusioner les differents elements de mon tableau
            );
        }

        return new SymfonyRoute($uri, [       // c'est grace au donnees qui sont passe avec cette route qu'on va savoir nous quelle action ensuite on souhaite executer  lorsque cette route precisement va etre appeler, on va recuperer toutes les routes qui sont dans routes.php
            '_controller' => $controller,
            '_method' => $method,
        ],                                   
        methods: [$httpMethod],
        options: [
            'utf8' => true,
        ]);
    }

    protected static function checkIfActionExists(string $controller, string $method): bool   // cette methode va nous permettre de faire ma verification (methode protege static) va nous retourner un boolean true si la methode existe ou false si il n'exsiste pas
    {
        return class_exists($controller) && is_subclass_of($controller, AbstractController::class) && method_exists($controller, $method);  // verifier si la classe exsiste et (&&) verifier si la methode exsiste bel et bien dans cette classe, la fonction is_subclass_of c'est pour verifier qu'on a bel et bien une classe enfant de AbstractController.
    }
}
