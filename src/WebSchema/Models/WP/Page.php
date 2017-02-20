<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 16/02/2017
 * Time: 12:45
 */

namespace WebSchema\Models\WP;

class Page extends Post
{
    const POST_TYPE = 'page';

    /**
     * @param \WP_Post $post
     * @return bool
     */
    protected static function isValidPostType(\WP_Post $post)
    {
        return ($post->post_type == self::POST_TYPE);
    }

    /**
     * @param array $types
     */
    protected static function addMetaBox(array $types = [])
    {
        parent::addMetaBox([self::POST_TYPE]);
    }
}