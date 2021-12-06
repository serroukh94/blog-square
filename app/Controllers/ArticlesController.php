<?php declare(strict_types = 1);

namespace App\Controllers;

use Ben\Foundation\AbstractController;

use App\Models\Post;
use Cocur\Slugify\Slugify;
use Ben\Foundation\View;

class ArticlesController extends AbstractController  //  c'est une classe parent, donc tous les controlleurs devront heriter forcement de la classe parent 
{
    public function articlePage(): void
    {
        $posts = Post::withCount('comments')->orderBy('id', 'desc')->get();   // la methode 'withCount' va me permettre de recuperer le nombre (un entier) d'entrée de la table comment qui sont concernée par la relation que j'ai créer
        View::render('article', [     
            'posts' => $posts,
        ]);
        
    }
}    