<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 23/02/2017
 * Time: 16:22
 */

namespace WebSchema\Controllers;

use WebSchema\Models\WP\Page;
use WebSchema\Models\WP\Post;

class PostController extends Controller
{
    protected static $instance;

    public function __construct()
    {
        parent::__construct();

        $this->addAction('wp_head', function () {
            global $post;

            if ($post) {
                ob_start();
            }
        }, 1);

        $this->addAction('wp_head', function () {
            global $post;

            if ($post) {
                $this->printSchema($post, ob_get_flush());
            }
        }, 100);
    }

    /**
     * Prints a JSON-LD schema representation of the post
     *
     * @param \WP_Post $post
     * @param string   $data
     */
    private function printSchema(\WP_Post $post, $data)
    {
        //prevent re-adding micro-data if already present
        if ($post->ID && !preg_match('/application\/ld\+json/i', $data)) {
            switch ($post->post_type) {
                case Page::POST_TYPE:
                    $model = Page::get($post->ID);
                    break;

                default:
                    $model = Post::get($post->ID);
                    break;
            }

            if ($model && ($json = $model->getJSONScript())) {
                print $json . PHP_EOL;
            }
        }
    }
}