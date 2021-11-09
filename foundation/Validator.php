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

           $validator->addRule('password', function (string $field, mixed $value, array $params, array $fields) {
            $user = Authentication::get();
            return password_verify($value, $user->password);
        }, '{field} est erroné');  
        
        $validator->addRule('required_file', function (string $field, mixed $value, array $params, array $fields) {  // 'required_file' va me permettre de verifier si le fichier en question est bien present donc pour notre fichier File au niveau de la $_Files et egalement je vais devoir verifier si il n y pas eu des erreurs lors de l'upload
            return isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK;  // on va tester s'il existe dans la $_FILES une clef qui porte le nom de notre champ et ensuite on a fait un illogique pour verifier qu'il n'est pas d'erreur lors de l'upload de nos fichiers, et pour verifier ca on va acceder a la cle 'errors' pour mon fichier dans la $_Files et on va regarder que ca valeur et bien egale a la valeur de la constante 'UPLOAD_ERR_OK'
        }, '{field} est obligatoire');
        
        // on va verifier le fichier qui etait uploader est bien une image  
        $validator->addRule('image', function (string $field, mixed $value, array $params, array $fields) {  
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                return str_starts_with($_FILES[$field]['type'], 'image/'); //  la fonction 'str_starts_with' verifier que le type commence boen par 'image/' 
            }
            return false;   // si jamais le fichier n'as pas correctement etait uploader dans ce cas la on retrun 'false'
        }, '{field} doit être une image');

        // cette regles va nous permettre de regarder si l'image est bien carré ou pas 
        $validator->addRule('square', function (string $field, mixed $value, array $params, array $fields) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {   //on a utiliser cette condition de base pour s'assurer qu'il est bien un fichier été uploader 
                [$width, $height] = getimagesize($_FILES[$field]['tmp_name']);  //on utiliser la fonction 'getimagesize' pour recuperer la largeur et la hauteur de l'image qui a été uploader 
                return $width === $height;   // on va retourner une comparaison si largeur est egale a la hauteur, donc si c'est bien ca c'est 'true'
            }
            return false;
        }, '{field} doit être carré (hauteur = largeur)');

    }
}