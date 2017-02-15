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
        add_action('delete_post', [self::class, 'actOnParent'], 10, 2);

        static::bootCollection();
    }

    /**
     * @param int $id
     */
    public static function actOnParent($id)
    {
        if (($post = get_post($id)) && ($post->post_type == self::POST_TYPE)
            && ($model = Attachment::get($post->ID))
        ) {
            $model->saveParent();
        }
    }

    private function saveParent()
    {
        $parent = Post::get(get_post($this->id)->post_parent);
        $parent->save();
    }
}