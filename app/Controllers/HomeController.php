<?php declare(strict_types = 1);

namespace App\Controllers;   

//export mes class
use Ben\Foundation\AbstractController;   
use Ben\Foundation\Authentication as Auth;
use Ben\Foundation\Session;
use Ben\Foundation\Validator;
use Ben\Foundation\View;


class HomeController extends AbstractController   
{
    public function index(): void
    {
        if (!Auth::check()) {            // (!) ici c'est le contraire on verifier si la personne n'est pas authentifier 
            $this->redirect('login.form');  // dans ce cas la on va le redegirer vers le formulaire de connexion 
        }

        $user = Auth::get();   //  on va commencer par recuperer le membre en question 

        View::render('home', [   // importer notre classe 'view' pour rendre notre vue 'home'
            'user' => $user,     
        ]);
    }


    public function updateName(): void      // le formulaire lorsque sera soummis on viendra excuter les instructions de la methode 'update' name 
    {
        if (!Auth::check()) {              //  on verifier si la personne n'est pas authentifier 
            $this->redirect('login.form');  // et bien on la rediriger 
        }

        $validator = Validator::get($_POST);     // on a creer un validator on utilisant les donnees dans le tableau post 
        $validator->mapFieldsRules([             // ici on aura un seul champ a venir pour verifier
            'name' => ['required', ['lengthMin', 5]],    // c'est 'name'
        ]);

        // on va utiliser la methode validate de valitron pour verifier si il y a des erreurs ou pas 
        if (!$validator->validate()) {          //(!) c'est pour recuperer le boolean contraire 
            Session::addFlash(Session::ERRORS, $validator->errors());      // si jamais il y a des erreurs on commence par ajouter les donnees flash a afficher, on a commencer par renseigner une valeur pour notre variable de session flash 'errors', donc on recupere l'ensemble des erreurs avec la methode errors de valitron
            Session::addFlash(Session::OLD, $_POST);   // on va stocker les valeurs de mes champs qui sont dans la superGlobal POST, on ajoutant une variable de session flash a la clef de ma constante 'old'
            $this->redirect('home');         // une fois que ca c'est fait, on redirige notre utilisateur vers son espace membre 
        }

        $user = Auth::get();    // recuperer notre utilisateur actuellement authentifier avec la methode get qu'on a creer dans la classe authentication.
        $user->name = $_POST['name'];  // on modifie la valeur du champ name, on modifiant la proprite name, c'est ca l'interet d'utiliser un ORM, donc ca nouvelle valeur ca sera le contenue de POST 'name'
        $user->save();       // pour que les modifications soient enregistrer dans ma table on utilise la methode 'save'


        // une fois que la mise a jour a ete faite on va ajouter un message de status, une variable de session flash status pour venir indiquer que le nom a bien ete mise a jour.
        Session::addFlash(Session::STATUS, 'Votre nom a été mis à jour !');
        $this->redirect('home');
    }

    public function updateEmail(): void      // le formulaire lorsque sera soummis on viendra excuter les instructions de la methode 'update' Email
    {
        if (!Auth::check()) {              //  on verifier si la personne n'est pas authentifier 
            $this->redirect('login.form');  // et bien on la rediriger 
        }

        $validator = Validator::get($_POST);     // on a creer un validator on utilisant les donnees dans le tableau post 
        $validator->mapFieldsRules([             // ici on aura un seul champ a venir pour verifier
            'email' => ['required', 'email',['unique', 'email', 'users']],    // c'est 'email' , et doit avoir un format d'adress email, on a utiliser notre regle qu'on avait creer, que la valeur doit etre 'unique' pour le champ 'email' dans la table 'users' 
        ]);

        // on va utiliser la methode validate de valitron pour verifier si il y a des erreurs ou pas 
        if (!$validator->validate()) {          //(!) c'est pour recuperer le boolean contraire 
            Session::addFlash(Session::ERRORS, $validator->errors());      // si jamais il y a des erreurs on commence par ajouter les donnees flash a afficher, on a commencer par renseigner une valeur pour notre variable de session flash 'errors', donc on recupere l'ensemble des erreurs avec la methode errors de valitron
            Session::addFlash(Session::OLD, $_POST);   // on va stocker les valeurs de mes champs qui sont dans la superGlobal POST, on ajoutant une variable de session flash a la clef de ma constante 'old'
            $this->redirect('home');         // une fois que ca c'est fait, on redirige notre utilisateur vers son espace membre 
        }

        $user = Auth::get();    // recuperer notre utilisateur actuellement authentifier avec la methode get qu'on a creer dans la classe authentication.
        $user->email = $_POST['email'];  // on modifie la valeur du champ email, on modifiant la proprite email, c'est ca l'interet d'utiliser un ORM, donc ca nouvelle valeur ca sera le contenue de POST 'email'
        $user->save();       // pour que les modifications soient enregistrer dans ma table on utilise la methode 'save'


        // une fois que la mise a jour a ete faite on va ajouter un message de status, une variable de session flash status pour venir indiquer que le nom a bien ete mise a jour.
        Session::addFlash(Session::STATUS, 'Votre adresse e-mail a été mise à jour !');
        $this->redirect('home');
    }


    public function updatePassword(): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'password_old' => ['required', 'password'],   // cette regle 'password' permet de verifier que le contenue de 'password_old' est bien le mdp actuel de mon utilisateur 
            'password' => ['required', ['lengthMin', 8], ['equals', 'password_confirmation']],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            $this->redirect('home');
        }

        $user = Auth::get();
        $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $user->save();

        Session::addFlash(Session::STATUS, 'Votre mot de passe a été mis à jour !');
        $this->redirect('home');
    }
}