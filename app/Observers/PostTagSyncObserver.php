<?php

namespace App\Observers;

use App\Models\Post;

class PostTagSyncObserver
{
    public function created(Post $post)
    {
        $this->syncTags($post);
    }

    public function updating(Post $post)
    {
        $this->syncTags($post);
    }

    private function syncTags(Post $post)
    {
        $tags = request()->input('tags', []); // Access tags from request
        $post->syncTags($tags);
    }
}
