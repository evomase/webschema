<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 06/03/2017
 * Time: 16:28
 */

namespace WebSchema\Tests\Models\AMP\Rules;

use Masterminds\HTML5;
use WebSchema\Models\AMP\Rules\Images;

class ImagesTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $html = <<<HERE
<html><head></head><body>
<img src="random1.jpg" alt="what" layout="fixed" />
<img src="random2.jpg" alt="what" layout="layout" />
<img src="random2.jpg" alt="what" />
</body></html>
HERE;
        $document = (new HTML5())->loadHTML($html);
        $rule = new Images($document);
        $rule->parse();

        $images = $document->getElementsByTagName('amp-img');
        $this->assertEquals(3, $images->length);
        $this->assertEquals(0, $document->getElementsByTagName('img')->length);

        $this->assertEquals('fixed', $images->item(0)->getAttribute('layout'));
        $this->assertEquals('responsive', $images->item(2)->getAttribute('layout'));
    }
}