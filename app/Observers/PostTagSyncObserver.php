<?php

namespace App\Observers;

use Modules\Post\App\Models\Post;

class PostTagSyncObserver
{
    public function creating(Post $post)
    {
        $this->syncTags($post);
    }

    public function updating(Post $post)
    {
        $this->syncTags($post);
    }

    private function syncTags(Post $post)
    {
        $tags = $post->request->get('tags', []); // Access tags from request
        $post->syncTags($tags);
    }
}
