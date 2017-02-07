<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 07/02/2017
 * Time: 17:08
 */

namespace WebSchema\Factory\WP;

use WebSchema\Factory\Factory;
use WebSchema\Models\WP\Post;

class PostFactory extends Factory
{
    public static function boot()
    {
        Post::boot();
    }
}