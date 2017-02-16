<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/02/2017
 * Time: 16:54
 */

namespace WebSchema\Models\WP;

class Attachment extends Post
{
    const POST_TYPE = 'attachment';

    protected $data = [];

    public static function boot()
    {
        add_action('add_attachment', [self::class, 'actOnParent'], 10, 2);
        add_action('deleted_post', [self::class, 'actOnParent'], 10, 2);

        static::bootCollection();
    }

    /**
     * @param \WP_Post $post
     * @return bool
     */
    protected static function isValidPostType(\WP_Post $post)
    {
        return ($post->post_type == self::POST_TYPE);
    }

    /**
     * @param int $id
     */
    public static function actOnParent($id)
    {
        if ($model = Attachment::get($id)) {
            $model->saveParent();
        }
    }

    private function saveParent()
    {
        $post = get_post($this->id);

        switch ($post->post_type) {
            case Page::POST_TYPE:
                $parent = Page::get($post->ID);
                break;

            default:
                $parent = Post::get($post->ID);
                break;
        }

        $parent->save();
    }
}