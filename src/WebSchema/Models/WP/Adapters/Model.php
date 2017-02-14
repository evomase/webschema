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
    public function getHeadline()
    {
        return $this->post->post_title;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return get_userdata($this->post->post_author)->display_name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return get_the_excerpt($this->post);
    }
}