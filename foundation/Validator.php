<?php declare(strict_types = 1);

namespace Ben\Foundation;

use Illuminate\DataBase\Capsule\Manager as Capsule ;
use Valitron\Validator as ValitronValidator;

class Validator
{
    public static function get(array $data): ValitronValidator   // c'est pour creer un nouveau validator en utilisont la methode static get pour mettre tous en place 
    {
        $validator = new ValitronValidator(
            data: $data, 
            lang: 'fr'
        );
        $validator->labels(require ROOT.'/resources/lang/validation.php');     
        static::addCustomRules($validator);
        return $validator;
    }
    
    protected static function addCustomRules(ValitronValidator $validator): void
    {
        // Custom rules here
        $validator->addRule('unique', function (string $field, mixed $value, array $params, array $fields) {  // la methode 'addRule' pour ajouter une regle a mon validator, on a indiquer le nom de la regle 'unique' pour dire que la valeur doit etre unique dans une table pour un champ en particulier et ensuite une fonction qui va indiquer la logique de validation que doit-il avoir.
                                    // cette fontion recuperera le nom du champ concerne dans un parametre field,  la valeur de ce champ, les parametres qu'on a utiliser avec la regle,.
            return !Capsule::table($params[1])->where($params[0], $value)->exists(); // ensuite ca retourne un boolean, si jamais la fonction retourne 'true' ce que tous c'est bien passé.
                            // on a utiliser la methode 'table' qui nous permet d'indiquer dans quelle table qu'on souhaite faire la verification 
        }, '{field} est invalide');   // si retourne 'false' il y a une erreur et on va afficher ce message. 
                                      // {field} si par exemple le champ s'appele email et bien se indiquera 'email est invalide' .
    }
}