<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 17/03/2017
 * Time: 17:15
 */

namespace WebSchema\Tests\Models\AMP\Rules\SocialMedia;

use Masterminds\HTML5;
use WebSchema\Models\AMP\Rules\SocialMedia\Pinterest;

class PinterestTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $html = <<<HERE
<html><head></head>
<body>
<a href="https://www.pinterest.com/pin/139752394664775546/"
   data-pin-do="embedPin">
</a>
</body>
</html>
HERE;
        $document = (new HTML5())->loadHTML($html);
        $rule = new Pinterest($document);
        $rule->parse();

        $this->assertEquals(0, $document->getElementsByTagName('a')->length);

        $head = $document->getElementsByTagName('head')->item(0);
        $this->assertEquals('amp-pinterest',
            $head->getElementsByTagName('script')->item(0)->getAttribute('custom-element'));

        $this->assertEquals(1, $document->getElementsByTagName('amp-pinterest')->length);
    }
}