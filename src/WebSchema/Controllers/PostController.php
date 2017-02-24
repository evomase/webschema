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

    protected function __construct()
    {
        add_action('wp_head', function () {
            ob_start();
        }, 1);

        add_action('wp_head', function () {
            $this->printJSON();
        }, 100);
    }

    private function printJSON()
    {
        global $post;

        $contents = ob_get_contents();
        ob_end_flush();

        //prevent re-adding micro-data if already present
        if ($post->ID && !preg_match('/application\/ld\+json/i', $contents)) {
            switch ($post->post_type) {
                case Page::POST_TYPE:
                    $model = Page::get($post->ID);
                    break;

                default:
                    $model = Post::get($post->ID);
                    break;
            }

            if ($model && ($json = $model->getJsonScript())) {
                print $json . PHP_EOL;
            }
        }
    }
}