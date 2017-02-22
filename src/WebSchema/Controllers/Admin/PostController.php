<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 17/02/2017
 * Time: 13:17
 */

namespace WebSchema\Controllers\Admin;

use WebSchema\Controllers\Controller;

class PostController extends Controller
{
    protected static $instance;

    protected function __construct()
    {
        add_action('admin_enqueue_scripts', function () {
            $this->enqueueScripts();
        });

        add_action('admin_enqueue_scripts', function () {
            $this->enqueueStyles();
        });
    }

    private function enqueueScripts()
    {
        global $hook_suffix;

        if (in_array($hook_suffix, ['post.php', 'post-new.php'])) {
            wp_enqueue_script('rainbow', WEB_SCHEMA_DIR_URL . '/resources/js/rainbow/rainbow.min.js', [], '2.1.2',
                true);
            wp_enqueue_script('web-schema-structured-data', WEB_SCHEMA_DIR_URL . '/resources/js/structured-data.js',
                ['rainbow'], WEB_SCHEMA_VERSION, true);
        }
    }

    private function enqueueStyles()
    {
        global $hook_suffix;

        if (in_array($hook_suffix, ['post.php', 'post-new.php'])) {
            wp_enqueue_style('web-schema-structured-data', WEB_SCHEMA_DIR_URL . '/resources/css/structured-data.css',
                [], WEB_SCHEMA_VERSION);

            wp_enqueue_style('rainbow', WEB_SCHEMA_DIR_URL . '/resources/js/rainbow/css/rainbow.min.css',
                ['web-schema-structured-data'], '2.1.2');
        }
    }
}