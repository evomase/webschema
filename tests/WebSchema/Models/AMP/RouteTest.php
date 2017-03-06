<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 06/03/2017
 * Time: 17:02
 */

namespace WebSchema\Tests\Models\AMP;

use WebSchema\Models\AMP\Route;
use WebSchema\Services\AMP\RouteService;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    private static $serverVars;

    public static function setUpBeforeClass()
    {
        self::$serverVars = $_SERVER;

        RouteService::boot();
    }

    public static function tearDownAfterClass()
    {
        $_SERVER = self::$serverVars;
        $_GET = [];
    }

    public function testRegister()
    {
        $post = get_post(wp_insert_post([
            'post_title'  => 'RouteTest',
            'post_status' => 'publish'
        ]));

        $_SERVER['PATH_INFO'] = '/?p=' . $post->ID;
        apply_filters('do_parse_request', true);

        $this->assertFalse(Route::getInstance()->isAMP());

        $_GET[Route::QUERY_VAR] = 'on';

        apply_filters('do_parse_request', true);
        apply_filters('request', []);

        $this->assertTrue(Route::getInstance()->isAMP());

        //test pretty/non-pretty url
        unset($_GET[Route::QUERY_VAR]);

        $_SERVER['PATH_INFO'] = '/amp/?p=' . $post->ID;
        $_SERVER['PHP_SELF'] = '/index.php';

        apply_filters('do_parse_request', true);

        $this->assertTrue(Route::getInstance()->isAMP());

        $this->assertEquals('/?p=' . $post->ID, $_SERVER['PATH_INFO']);
        $this->assertEquals('/index.php/?p=' . $post->ID, $_SERVER['REQUEST_URI']);

        $this->assertArrayHasKey(Route::QUERY_VAR, apply_filters('request', []));
    }
}