<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Searchable;

class ArticleComment extends Model
{
    use HasFactory, Searchable;

    protected array $searchableFields = ['comment', 'user.name', 'article.title'];

    protected $fillable = [
        'article_id',
        'user_id',
        'rating',
        'comment',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
