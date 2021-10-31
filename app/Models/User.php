<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model     // pour pouvoir utiliser L'ORM Eloquent et bien toutes nos classe de Model vont devoir etendre la classe Model de Elequant
{           // ma classe User en etendant ma classe Model me permettre de manipuler sans aucun probleme une table en BDD qui s'apelle Users
   
    protected $fillable = [           //  'fillable' est une propriete indique les differents champs qu'on va pouvoir utiliser dans de l'affectation de masse. 
        'name', 'email', 'password',   // les 3 champs pour lesquelles on va indiquer des valeurs quant on creer un nouvel utilisateur  
    ];
  
}

