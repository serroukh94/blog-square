<?php declare(strict_types = 1);

namespace App\Controllers;

use App\Classes\Mail;
use Ben\Foundation\AbstractController;
use Ben\Foundation\Authentication as auth;
use Ben\Foundation\Session;
use Ben\Foundation\Validator;
use Ben\Foundation\View;

class ContactController extends AbstractController {

    public function form(): void    {

    
        {
            if (!Auth::check()) {       
                $this->redirect('login.form');   
            }
    
            $validator = Validator::get($_POST);     
            $validator->mapFieldsRules([   
                'surname'   => ['required', ['lengthMin', 5]],  
                'firstname' => ['required', ['lengthMin', 5]], 
                'email'     => ['required', 'email'],  
                'message'   => ['required', ['lengthMin', 5]],         
            ]);
    
            if (!$validator->validate()) {     // si jamais il y a une erreur on va devoir faire 3 choses 
    
                Session::addFlash(Session::ERRORS, array_column($validator->errors(), 0)); // la 1ere, on va devoir ajouter une variable de session flash errors pour venir indiquer les differents messages d'erreur que j'ai recuperer avec mon validator  
                Session::addFlash(Session::OLD, $_POST);                                   // la 2eme on va devoir ajouter une variable de session flash pour recuperer les anciennes valeurs de mes champs pour reremplir le formulaire correctement avec le nom complet et l'adresse email qui avait etait utiliser 
                $this->redirect('index.form');                                            
            } 
    
            View::render('index');  
        }
    
        {
            $mail = new Mail();
            $mail-> send("serroukh94@gmail.com", "mohamed", 'essai'
            , "essi");
            
        };
       }



}