<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model  // par defaut quant on creer un nouveau models la table qui lui est associé c'est le nom de la classe en minuscule avec un 'S' a la fin 
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'chapô', 'body', 'reading_time', 'img',
    ];

    public function comments(): HasMany    // avec la relation 'HasMany' on va recuperer une instance de meme nom, qui du coup est une relation dans laquelle on va avoir une entrée dans une table qui va etre liée a plusieur autre entrées dans une autre table. (on va avoir un poste qui va etre liée a une infinité des commentaires ) 
    {
        return $this->hasMany(Comment::class)->orderBy('id', 'desc');  // on va recuperer la liste des commentaires liée au post sur lequel je suis actuellement 
    }
}


