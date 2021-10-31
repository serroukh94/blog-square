<?php declare(strict_types = 1);

namespace App\Controllers;   

use App\Models\User;
use Ben\Foundation\AbstractController;
use Ben\Foundation\Authentication as auth;
use Ben\Foundation\Session;
use Ben\Foundation\Validator;
use Ben\Foundation\View;

class AuthController extends AbstractController  //  c'est une classe parent, donc tous les controlleurs devront heriter forcement de la classe parent 
{
    public function registerForm(): void
    {
        if (Auth::check()) {           // ce formulaire ne devra etre accessible que pour les personnes  qui ne sont pas authentifier si jamais la personne qui t'ont d'acceder a cette URI inscription et deja authentifier dans ce cas la on va la redigirer vers son espace utilisateur     
            $this->redirect('home');   // donc cette methode check permet de verifier si le visiteur actuel est authentifier ou pas 
        }                              // alors on recupere un boolean si 'true' on le rediriger vers la page d'accueil
        View::render('auth.register');  // on a utiliser notre classe view et sa methode render pour generer notre vue a l'aide de twig.
    }

    public function register(): void  // on a creer la methode 'register' 
    {   // ici on aura toute la logique pour venir enregistrer une nouvelle entree pour la table users pour inscrire une nouvelle utilisateur 
        if (Auth::check()) {     
            $this->redirect('home');
        }

        $validator = Validator::get($_POST);    // on a creer une variable 'validator', en utilisant la classe 'validator', la methode get et passer en argument le contenue de la superGlobal 'POST' puisque c'est un tableau et cette superGlobal contient different champ de mon formulaire   
        $validator->mapFieldsRules([          // avec la methode 'mapFieldsRules' on pourra indiquer comment utiliser nos formulaires
            'name' => ['required', ['lengthMin', 5]],   // on a commencer par le champ 'name' comme sur notre formulaire, l'utilisation de la regle required permet d'indiquer qu'une valeur est requise si jamais ici ma superGlobal POST ne contient pas du nom est bien je vais me recuperer une erreur  qui va  m'indique que ce champ est requis 
                                    // la 2eme regles 'lengthMin' indique la taille minimal de ma chaine de caractere avec une valeur '5'.
            'email' => ['required', 'email', ['unique', 'email', 'users']], //['unique', 'email', 'users'] regle personnalisés, 'unique' unique valeur, 'email' c'est le nom du champ, est dans quelle table on doit verifier ce champ c'est dans la table 'users'.
            'password' => ['required', ['lengthMin', 8], ['equals', 'password_confirmation']], // 'equal' ca veut dire que mon password doit etre egal a mon password confirmation
        ]);

        if (!$validator->validate()) {     // si jamais il y a une erreur on va devoir faire 3 choses 

            Session::addFlash(Session::ERRORS, array_column($validator->errors(), 0)); // la 1ere, on va devoir ajouter une variable de session flash errors pour venir indiquer les differents messages d'erreur que j'ai recuperer avec mon validator  
            Session::addFlash(Session::OLD, $_POST);                                   // la 2eme on va devoir ajouter une variable de session flash pour recuperer les anciennes valeurs de mes champs pour reremplir le formulaire correctement avec le nom complet et l'adresse email qui avait etait utiliser 
            $this->redirect('register.form');                                          // la 3eme on va devoir rederiger mon utilisateur vers le formulaire d'inscription pour qu'il recupere ce formulaire 
        } 

        // INSERT into : 
        $user = User::create([        // la methode create
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),   // on va devoir utiliser la fonction 'password_hash' pour le crypté au niveau de notre BDDet du coup notre table d'utilisateur on ne verra jamais en clair le mdp de notre utilisateur et bien heureusement pour la sécurité et donc la seule facon qu'on aura de verifier le mdp de notre utilisateur est correcte lorsque essaye de se connecter et bien ca sera d'utiliser le password_verify qu'on a utiliser dans la classe authentication. 
        ]);         //notre utilisateur est bien creer                           // la constante 'PASSWORD_DEFAULT' va utiliser a chaque fois le protocol de cryptage.  


        Auth::authenticate($user->id);    // pour authentifier cet utilisateur c'est utiliser ma classe authentication, vue qu'on utilise l'ORM Eloquent on accede a nos differentes colonnes en les utilisant comme des proprietes 
        $this->redirect('home');          // une fois qu'il est inscrit et qu'il est authentifier en le rederige vers son espace membre, et c'est la route 'home'

    }
}