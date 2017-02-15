<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/02/2017
 * Time: 17:19
 */

namespace WebSchema\Services\WP;

use WebSchema\Models\WP\Attachment;
use WebSchema\Models\WP\Post;
use WebSchema\Services\Service;

class PostService extends Service
{
    public static function boot()
    {
        Post::boot();
        Attachment::boot();
    }

    public static function shutdown()
    {
        Post::clearCollection();
        Attachment::clearCollection();
    }
}