<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Contracts\IsComment;
use App\Models\User\User;

use Database\factories\CommentFactory;

class Comment extends Model implements IsComment
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'comments';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['content', 'user_id', 'parent_id'];

    protected static function newFactory(): CommentFactory
    {
        return CommentFactory::new();
    }

    /**
     * Get the parent commentable model (post or video).
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Return the user relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Return the replies relationship.
     */
    public function replies(): MorphMany
    {
        return $this->morphMany(static::class, 'commentable');
    }
}
