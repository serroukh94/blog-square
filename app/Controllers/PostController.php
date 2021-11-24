<?php declare(strict_types = 1);

namespace App\Controllers;   

use App\Classes\Mail;
use Mailjet\Client;
use Mailjet\Resources;
use App\Models\Comment;
use App\Models\Post;
use Cocur\Slugify\Slugify;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ben\Foundation\AbstractController;
use Ben\Foundation\Authentication as auth;
use Ben\Foundation\Exceptions\HttpException;
use Ben\Foundation\Session;
use Ben\Foundation\Validator;
use Ben\Foundation\View;

class PostController extends AbstractController  //  c'est une classe parent, donc tous les controlleurs devront heriter forcement de la classe parent 
{
    public function index(): void
    {
        $posts = Post::withCount('comments')->orderBy('id', 'desc')->get();   // la methode 'withCount' va me permettre de recuperer le nombre (un entier) d'entrée de la table comment qui sont concernée par la relation que j'ai créer
        View::render('index', [     
            'posts' => $posts,
        ]);
    }

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
    

    public function show(string $slug): void
    {
        try {
            $post = Post::withCount('comments')->where('slug', $slug)->firstOrFail(); // on va recuperer pour le post le nombre des commentaires 
        } catch (ModelNotFoundException) {
            HttpException::render();
        }

        View::render('posts.show', [
            'post' => $post,
        ]);
    }

    public function comment(string $slug): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }

        $post = Post::where('slug', $slug)->firstOrFail();

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'comment' => ['required', ['lengthMin', 3]],
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('posts.show', ['slug' => $slug]);
        }

        Comment::create([           // pour creer notre nouveau commentaire
            'body' => $_POST['comment'],   // on a indiquer POST comment 
            'user_id' => Auth::id(),      // on utiliser notre classe auth et sa methode id pour recuperer l'identifiant 
            'post_id' => $post->id,       
        ]);

        Session::addFlash(Session::STATUS, 'Votre commentaire a été publié !');
        $this->redirect('posts.show', ['slug' => $slug]);
    }

    public function delete(string $slug)
    {
        if (!Auth::checkIsAdmin()) {
            $this->redirect('login.form');
        }

        $post = Post::where('slug', $slug)->firstOrFail();  

        unlink(sprintf('%s/public/img/%s', ROOT, $post->img)); // la fonction 'unlink' pour supprimer un fichier, on a indiquer le chemin vers le fichier qu'on souhaite suprimer
        $post->delete();

        $this->redirect('index');
    }

    public function create(): void  //la methode create va nous permettre de rendre la vue pour creer un nouveau post  
    {
        if (!Auth::checkIsAdmin()) {       // verfier si quelqu'un n'est pas admin
            $this->redirect('login.form');  // si jamais la personne n'est pas admin donc on va la rediriger vers le formulaire de connexion  
        }

        View::render('posts.create');  //  on va faire un render de la vue qui se trouve dans le sous dossier post et qui s'appelle 'create'
    }

    public function store(): void    // avec la methode store on va enregistrer un nouvel article 
    {
        if (!Auth::checkIsAdmin()) {          
            $this->redirect('login.form');
        }

        $validator = Validator::get($_POST + $_FILES);   
        $validator->mapFieldsRules([
            'title' => ['required', ['lengthMin', 3]],  // titre d'article
            'chapô' => ['required', ['lengthMin', 3]],  
            'post' => ['required', ['lengthMin', 3]],   // contenue de l'article
            
            'file' => ['required_file', 'image', 'square'],  // on va pouvoir recuperer les differentes valeur en lien avec les fichiers uploader a l'aide de la '$_FILES'. square = carré
        ]);

        if (!$validator->validate()) {
            Session::addFlash(Session::ERRORS, $validator->errors());  // on a utiliser notre classe session pour ajouter nos erreur a nos variable de session flash 
            Session::addFlash(Session::OLD, $_POST);   // pour recuperer le contenue des anciennes valeurs
            $this->redirect('posts.create');   
        }

        
        $slug = $this->slugify($_POST['title']);   
        $ext = pathinfo(    // on a creer la variable 'ext' et on a utiliser la fonction 'pathinfo' pour recuperer des informations par rapport un fichier 
            $_FILES['file']['name'], // et donc on lui a renseigner en premier argument le nom reel de notre fichier par rapport a la superglobals 'file' et sa clef 'name' 
            PATHINFO_EXTENSION       // on a indiquer qu'on veut recuperer l'extension du fichier on indiquant la constante 'PATHINFO_EXTENSION'
        );
        $filename = sprintf('%s.%s', $slug, $ext); 

        if (!move_uploaded_file(    //  verifier si l'upload passe correctement 
            $_FILES['file']['tmp_name'],
            sprintf('%s/public/img/%s', ROOT, $filename)
        )) {
            Session::addFlash(Session::ERRORS, ['file' => [   // si l'upload passe mal
                'Il y a eu un problème lors de l\'envoi. Retentez votre chance !',
            ]]);
            Session::addFlash(Session::OLD, $_POST);  // on ajoute les valeurs de nos champs pour remplir le formulaire 
            $this->redirect('posts.create');
        }

        $post = Post::create([          // on a creer la variable 'post' qui va utiliser notre model 'Post' et on a utiliser la methode 'create' qui contient un tableau, on va indiquer pour chacun des ses element de ce tableau associatif le champ pour lequel on va creer une valeur  
            'user_id' => Auth::id(),    // le 'user_id' on va le recuperer a l'aide de la classe authentication puisque qu'on a une methode 'id' qui permet de recuperer l'identifiant de l'utilisateur  actuellement authentifier 
            'title' => $_POST['title'], // pour le title ca sera contenue dans la $_POST. 
            'slug' => $slug,            // le slug c'est le contenue de la variable slug
            'chapô'=> $_POST['chapô'],
            'body' => $_POST['post'],   // le body c'est contenue dans la la $_POST.
            'reading_time' => ceil(str_word_count($_POST['post']) / 238),  
            'img' => $filename,         //  l'image on indique le nom final de fichier qui est contenue dans le dossier img  dans le dossier public
        ]);

        // on va ajouter une nouvelle variable de session flash STATUS ou on va indiquer que le post a été publié 
        Session::addFlash(Session::STATUS, 'Votre post a été publié ! ');  
        $this->redirect('posts.show', ['slug' => $post->slug]);  
    }

    // Modification d'un post 
    public function edit(string $slug): void   
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }

        try {
            $post = Post::where('slug', $slug)->firstOrFail();
        } catch (ModelNotFoundException) {
            HttpException::render();
        }

        View::render('posts.edit', [
            'post' => $post,
        ]);
    }

    public function update(string $slug): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }

        $post = Post::where('slug', $slug)->firstOrFail();

        $validator = Validator::get($_POST);
        $validator->mapFieldsRules([
            'title' => ['required', ['lengthMin', 3]],
            'chapô' => ['required', ['lengthMin', 3]],
            'post' => ['required', ['lengthMin', 3]],
        ]);

        if (!$validator->validate()) {    // on va verifier si il 'y a un probleme lors de la validation 
            Session::addFlash(Session::ERRORS, $validator->errors());     
            Session::addFlash(Session::OLD, $_POST);
            $this->redirect('posts.edit', ['slug' => $post->slug]);
        }

        $post->fill([     // avec l'orm eloquant pour faire une mise a jour, on a utilisé la methode 'fill' qui va prendre un tableau des différents champ qu'on souhaite modifier 
            'title' => $_POST['title'],
            'chapô'=> $_POST['chapô'],
            'body' => $_POST['post'],
            'reading_time' => ceil(str_word_count($_POST['post']) / 238),
        ]);
        $post->save();  // une fois qu'on a indiqué ce qu'on souhaite mettre a jour, on a utilisé la methode 'save'  pour  la validation.

        Session::addFlash(Session::STATUS, 'Votre post a été mis à jour !');
        $this->redirect('posts.show', ['slug' => $post->slug]);
    }
    
    // enregister mon article, enregister le fichier qui est associé, l'image que lui est associé donc je vais vouloir creer un slug de mon titre d'article pour que celui ci puissent se retrouver dans mon url et ca va etre avec la bibliotheque cocur/slugify
    protected function slugify(string $title): string
    {
        $slugify = new Slugify();  // on a creer une variable 'slugify' qui sera nouvelle instance de la classe slugify  a laquelle on a acces grace a notre nouvelle depandance
        $slug = $slugify->slugify($title); // pour utiliser cette depandance on a creer une variable slug ensuite on a utiliser notre instance de slugify et on utiliser la methode 'slugify'sur la chaine de caractere pour laquelle on souhaite creer un slug pour nous c'est le 'title'
        $i = 1;
        $unique_slug = $slug;
        while (Post::where('slug', $unique_slug)->exists()) {  // la on va regarder dans mes post, ajouter une clause 'where' et regarder dans mes différentes entrées lesquelles le 'slug' va etre egale a mon slug qui censé etre unique, avec la methode 'exists' on va recuperer un boolean avec cette requete donc je vais recuperer 'true' si jamais le slug existe deja dans ma table, false si il n'existe pas    
            $unique_slug = sprintf('%s-%s', $slug, $i++);    // mettre a jour le 'slug' pour qu'il soit unique 
        }
        return $unique_slug;
    }
 
    

       

}