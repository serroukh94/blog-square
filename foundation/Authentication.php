<?php declare(strict_type = 1);

namespace Ben\Foundation;

use App\Models\User;

class Authentication
{
    protected const SESSION_ID = 'user_id';  // cette methode ca sera l'identifiant au niveau des variables de session qui va me permettre  de verifier si un utilisateur est connecte ou pas 

    public static function check(): bool      // la methode check: si jamais la variabe de session n'existe pas ca veut dire que la personne n'est pas connecte et si il existe ca veut dire qu'il est.
    {
        return (bool) Session::get(static::SESSION_ID);   // vue qu'on va retourner un boolean: false -> on a pas de 'user_id'
    }

    public static function checkIsAdmin(): bool    // 'checkIsAdmin' cette methode utilitaire va me permettre de verifier si une personne et non seulement bien authentifier mais qu'en plus de ca il a un role d'administrateur 
    {
        return static::check() && static::get()->role === 'admin';   // avec la methode get je recupere une instance de user 
    }

    public static function verify(string $email, string $password): bool     // cette methode va nous permettre de verifier les champs d'adresse email et mdp que l'utilisateur a renseigner dans le formulaire de conexion correspond bien a une entré que j'ai en BDD dans ma table users comme ca me permettre de gerer l'authentification d'un utilisateur
    {
        $user = User::where('email', $email)->first();            // on a utiliser la methode 'where' qui va me permettre de faire une clause where dans ma requete sql. et ensuite on va indiquer qu'on souhaite recuperer seulement le premier resultat de la recherche .
        return $user && password_verify($password, $user->password);   // verifier si user n'est pas 'null' et egalement verifier si le mdp associes a cette utilisateur est egale a celui qui est en BDD dans ma table users. 
    }

    public static function authenticate(int $id): void    
    {
        Session::add(static::SESSION_ID, $id);
    }

    public static function logout(): void      //  cette methode va me permettre de venir deconnecter mon utilisateur 
    {
        Session::remove(static::SESSION_ID);  // on a utiliser notre classe 'session' et sa methode 'remove' pour supprimer une variable de session  
    }

    public static function id(): ?int          
    {
        return Session::get(static::SESSION_ID);
    }

    public static function get(): ?User      // cette methode va nous permettra de recuperer les informations sur l'utilisateur qui est actuelement authentifier
    {                                        // soit je recuperer la valeur null si jamais l'utilisateur n'existe pas, soit je recupere une instance de ma classe User qui est dans app-Models
        return User::find(static::id());     
    }
}