<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 14/02/2017
 * Time: 12:20
 */

namespace WebSchema\Models\WP\Adapters;

use WebSchema\Models\StructuredData\Types\Interfaces\ArticleAdapter;
use WebSchema\Models\WP\Adapters\Traits\HasPublisher;

class Article extends Model implements ArticleAdapter
{
    use HasPublisher;

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
     * @return string
     */
    public function getHeadline()
    {
        return $this->post->post_title;
    }

    /**
     * @return string
     */
    public function getAuthorName()
    {
        return get_userdata($this->post->post_author)->display_name;
    }

    /**
     * @return string
     */
    public function getAuthorURL()
    {
        return get_userdata($this->post->post_author)->user_url;
    }
}