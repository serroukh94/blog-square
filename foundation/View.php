<?php declare(strict_types = 1);

namespace Ben\Foundation;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class View 
{
    public static function render(string $view, array $data = []): void    // cette methode ne retournera rien  par contre il prendra deux parametre le 1er ca sera le nom de la vue qu'on souhaite rendre et le 2eme ca sera un tableau des donnees qu'on souhaite passe a la vue et du coup par defaut il sera un tableau vide pour ne pas obliger a chaque fois a indiquer une valeur en argument comme ca on pourra simplement indiquer soit un argument pour indiquer la vue ou allors les deux si jamais il y a des donnees a transmettre 
    {
        $view = str_replace('.', '/', $view);  
        if (!static::viewExists($view)) {         
            throw new \InvalidArgumentException(            //  si la vue n'existe pas 
                sprintf('La vue %s n\'existe pas', $view)
            );
        }
        $twig = static::initTwig();           // une fois que la vue existe 
        echo $twig->render(
            sprintf('%s.%s', $view, Config::get('twig.template_extension')),
            $data
        );
    }

    protected static function viewExists(string $view): bool   
    {
        return file_exists(
            sprintf('%s/resources/views/%s.%s', ROOT, $view, Config::get('twig.template_extension'))    
        );
    }

    protected static function initTwig(): Environment       
    {
        $loader = new FilesystemLoader(ROOT.'/resources/views');
        $twig = new Environment($loader, [         // pour indiquer ou sont mes vues 
            'cache' => ROOT.'/cache/twig',       // element 'cache' va me permettre d'indiquer que je souhaite mettre en cache mes differentes vues 
            'auto_reload' => true,
        ]);
        foreach (Config::get('twig.functions') as $helper) {       // l'ajout de differents fonctions de 'twig.php' pour pouvoir utiliser dans mes templates
            $twig->addFunction(new TwigFunction($helper, $helper));  
        }
        return $twig;
    }

    public static function csrfField(): string  // cette methode va me retourner directement un champ html caché qui contiendra le "token' qui a eté generer pour proteger mon utilisateur de la faille CSRF
    {                                           // va me retourne une chaine de caractere
        return sprintf('<input type="hidden" name="_token" value="%s">', Session::get('_token'));    //   dans un champ caché
    }

    public static function method(string $httpMethod): string         
    {
        return sprintf('<input type="hidden" name="_method" value="%s">', $httpMethod);
    }

    public static function old(string $key, mixed $default = null): mixed    //  cette methode va me permettre de recuperer le contenue des champs qui avait eté précédemment rempli sur mon formulaire donc ca sera en particulier quand on aura des erreurs et qu"on souhaite remplir de nouveau les champs avec le contenue qu'ils avait precedemment pour eviter que notre utilisateur et a tout retaper 
    {
        $old = Session::getFlash(Session::OLD);   
        return $old[$key] ?? $default;
    }
}