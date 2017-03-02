<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 02/03/2017
 * Time: 11:58
 */

namespace WebSchema\Tests\Controller\Admin;

use WebSchema\Controllers\Admin\PostController;
use WebSchema\Tests\AbstractTestCase;

class PostControllerTest extends AbstractTestCase
{
    public static function setUpBeforeClass()
    {
        new PostController();
    }

    public function testEnqueueScripts()
    {
        global $hook_suffix, $wp_scripts;

        $old = $hook_suffix;

        $hook_suffix = 'post.php';

        do_action('admin_enqueue_scripts');

        $this->assertInstanceOf(\_WP_Dependency::class, $wp_scripts->query('web-schema-structured-data'));

        //reset
        $hook_suffix = $old;
    }

    public function testEnqueueStyles()
    {
        global $hook_suffix, $wp_styles;

        $old = $hook_suffix;

        $hook_suffix = 'post.php';

        do_action('admin_enqueue_scripts');

        $this->assertInstanceOf(\_WP_Dependency::class, $wp_styles->query('web-schema-structured-data'));

        //reset
        $hook_suffix = $old;
    }
}