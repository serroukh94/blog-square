<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = [
        'body', 'user_id', 'post_id',
    ];

    public function user(): BelongsTo   // la relation 'BelongsTo' va me permettre de recuperer l'entrée dans la table users grace a 'user_id' qui est contenue dans ma table comment 
    {
        return $this->belongsTo(User::class);
    }
}