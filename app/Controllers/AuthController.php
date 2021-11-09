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
        if (Auth::check()) {           // ce formulaire ne devra etre accessible que pour les personnes  qui ne sont pas authentifier si jamais la personne qui t'ont d'acceder a cette URI inscription et deja authentifier dans ce cas la on va la rediriger vers son espace utilisateur     
            $this->redirect('home');   // donc cette methode check permet de verifier si le visiteur actuel est authentifier ou pas 
        }                              // alors on recupere un boolean si 'true' on le rediriger vers la page d'accueil
        View::render('auth.register');  // on a utiliser notre classe view et sa methode render pour generer notre vue a l'aide de twig.
    }

    public function register(): void  // on a creer la methode 'register' 
    {   // ici on aura toute la logique pour venir enregistrer une nouvelle entree pour la table users pour inscrire un nouvel utilisateur 
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
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),   // on va devoir utiliser la fonction 'password_hash' pour le crypté au niveau de notre BDD et du coup notre table d'utilisateur on ne verra jamais en clair le mdp de notre utilisateur et bien heureusement pour la sécurité et donc la seule facon qu'on aura de verifier le mdp de notre utilisateur est correcte lorsque essaye de se connecter et bien ca sera d'utiliser le password_verify qu'on a utiliser dans la classe authentication. 
        ]);         //notre utilisateur est bien creer                           // la constante 'PASSWORD_DEFAULT' va utiliser a chaque fois le protocol de cryptage.  


        Auth::authenticate($user->id);    // pour authentifier cet utilisateur c'est utiliser ma classe authentication, vue qu'on utilise l'ORM Eloquent on accede a nos differentes colonnes en les utilisant comme des proprietes 
        $this->redirect('home');          // une fois qu'il est inscrit et qu'il est authentifier en le rederige vers son espace membre, et c'est la route 'home'

    }


    public function loginForm(): void     
    {
        if (Auth::check()) {
            $this->redirect('home');
        }

        View::render('auth.login');
    }

    public function login(): void   
    {
        if (Auth::check()) {           //  si la personne est deja authentifier on la rederige vers son espace membre
            $this->redirect('home');
        }

        // on va utiliser notre class 'validator' pour initialiser  le validator de valitron et ensuite on va indiquer les differentes regles pour nos deux champs: le champ email et le champ password
        $validator = Validator::get($_POST);     
        $validator->mapFieldsRules([       
            'email' => ['required', 'email'],  // c'est requi et doit avoir un format d'email 
            'password' => ['required'],         
        ]);  // une fois que ces regles vont etre passees et bien je vais verifier en BDD dans la table users si les email et password correspond un utilisateur qui existe 

        // donc on va faire une condition dans laquelle on rentrera si tout est ok au niveau du formulaire de connexion
        if ($validator->validate() && Auth::verify($_POST['email'], $_POST['password'])) {   // on va utiliser la methode validate qui renvoie 'true' si jamais le formulaire est correctement rempli et ensuite on a fait un 2eme element conditionnel qui va etre de verifier si l'adresse email et le mdp fournit correspand bien a un utilisateur dans ma tables users  
            $user = User::where('email', $_POST['email'])->first();        // j'ai utilisé ma classe user et ma methode 'where', je vais récupéré l'entré ou l'adresse email egal a l'adresse email renseigné dans le formulaire -> et j'indique que je vais recupéré seulement le 1er résultat positive 
            Auth::authenticate($user->id);                   // ici on a utilisé la methode authenticate de ma classe authentication qui me permet d'enregistrer en valable de session  l'identifiant de mon utilisateur pour pouvoir plus tard  le recupérer.
            $this->redirect('home');                       //  rederiger vers son espace membre             
        }

        Session::addFlash(Session::ERRORS, ['Identifiants erronés']);   // on utilisant la methode 'addFlash' on doit a voir retourner les erreurs et len anciennes valeurs de nos de formulaire pour que nos vue puisse avoir accées et affichent les erreurs et reremplis les champs sur laquelle on a utilisé la fonction 'old'
        Session::addFlash(Session::OLD, $_POST);         // ici on a ajouter les anciennes entré 
        $this->redirect('login.form');                  // une fois qu'on a enregistrer toutes ses variables de sessions flash on peut rederiger notre visiteur vers le formulaire de connexion la route 'login.form'
    }



    public function logout(): void          
    {
        if (Auth::check()) {              // si on veut se deconnecter et bien il faut etre connecte 
            Auth::logout();               // on a deja creer la methode 'logout' qui permet de supprimer la variable de session 'user_id' pour que notre utilisateur ne soit plus authentifier 
        }

        $this->redirect('login.form');   // si non en le redegira sur 'login.form'(formulaire de connexion)
    }

     

       
}