<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 14/02/2017
 * Time: 12:20
 */

namespace WebSchema\Models\WP\Adapters;

use WebSchema\Models\DataTypes\Interfaces\Adapter;

abstract class Model implements Adapter
{
    /**
     * @var \WP_Post
     */
    protected $post;

    public function __construct(\WP_Post $post)
    {
        $this->post = $post;
    }

    /**
     * @return false|string
     */
    public function getMainEntityOfPage()
    {
        return get_the_permalink($this->post);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return get_the_excerpt($this->post) ?: wp_trim_words($this->post->post_content);
    }

    /**
     * @return null|string
     */
    public function getImageURL()
    {
        $image = get_post_thumbnail_id($this->post);

        if (!$image && ($images = get_attached_media(['image/jpeg', 'image/png', 'image/gif'], $this->post))) {
            /**
             * @var \WP_Post $image
             */
            $image = current($images)->ID;
        }

        if (($image = wp_get_attachment_url($image)) && (strpos($image, 'attachment_id=') !== false)) {
            return null;
        }

        return (string)$image;
    }
}