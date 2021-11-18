<?php declare(strict_types = 1);

namespace Ben\Foundation;

use Ben\Foundation\Router\Router;

abstract class AbstractController  // 
{
    protected function redirect(string $name, array $data = []): void   // la methode redirect pour faire des redirection en indiquant le nom de la route ainsi qu'eventuellement ces parametres variables 
    {
        header(sprintf('Location: %s', Router::get($name, $data)));
        die;
    }
}