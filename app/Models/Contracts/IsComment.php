<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
interface IsComment
{
    public function commentable(): MorphTo;

    public function replies(): MorphMany;

    public function user(): BelongsTo;
}
