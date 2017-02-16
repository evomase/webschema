<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 14/02/2017
 * Time: 12:20
 */

namespace WebSchema\Models\WP\Adapters;

use WebSchema\Models\DataTypes\Interfaces\ArticleAdapter;

class Article extends Model implements ArticleAdapter
{
    /**
     * @return \DateTime
     */
    public function getDateModified()
    {
        return new \DateTime($this->post->post_modified, new \DateTimeZone(date_default_timezone_get()));
    }

    /**
     * @return \DateTime
     */
    public function getDatePublished()
    {
        return new \DateTime($this->post->post_date, new \DateTimeZone(date_default_timezone_get()));
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