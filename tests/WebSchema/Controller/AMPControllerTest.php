<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 02/03/2017
 * Time: 15:51
 */

namespace WebSchema\Tests\Controller;

use Masterminds\HTML5;
use Mockery as m;
use WebSchema\Controllers\AMPController;
use WebSchema\Models\AMP\DocumentParser;
use WebSchema\Models\AMP\Route;

class AMPControllerTest extends \PHPUnit_Framework_TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testOutputBufferOn()
    {
        $route = m::mock(Route::class)->makePartial();
        $route->shouldReceive('isAMP')->once()->andReturn(true);

        /**
         * @var Route $route
         */
        new AMPController($route, new DocumentParser(new HTML5()));

        $this->assertCount(1, ob_get_status(true));

        do_action('template_redirect');

        $this->assertCount(2, ob_get_status(true));

        //flush output buffer (due to it started in AMPController constructor
        ob_end_flush();
        AMPController::shutdown();
    }

    public function testParseHTML()
    {
        $expectedOutput = 'Hello world';

        $route = m::mock(Route::class)->makePartial();
        $route->shouldReceive('isAMP')->twice()->andReturn(true);

        $parser = m::mock(DocumentParser::class, [new HTML5()])->makePartial();
        $parser->shouldReceive('parse')->once()->andReturn($expectedOutput);

        /**
         * @var Route          $route
         * @var DocumentParser $parser
         */
        new AMPController($route, $parser);

        do_action('template_redirect');

        print 'This should not be printed';

        //remove this filter to enable testing of output string
        remove_action('shutdown', 'wp_ob_end_flush_all', 1);

        $this->expectOutputString($expectedOutput);
        do_action('shutdown');

        //reset
        AMPController::shutdown();
        add_action('shutdown', 'wp_ob_end_flush_all', 1);
    }
}