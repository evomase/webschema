<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 06/03/2017
 * Time: 16:38
 */

namespace WebSchema\Tests\Models\AMP\Rules;

use Masterminds\HTML5;
use WebSchema\Models\AMP\Rules\SVG;

class SVGTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $html = <<<HERE
<html><body>
<svg class="icon icon-mail-reply" aria-hidden="true" role="img"> 
<use href="#icon-mail-reply" xlink:href="#icon-mail-reply"></use>
</svg>
</body></html>
HERE;
        $document = (new HTML5())->loadHTML($html);
        $rule = new SVG($document);

        $this->assertTrue($document->getElementsByTagName('use')->item(0)->hasAttribute('href'));

        $rule->parse();

        $this->assertFalse($document->getElementsByTagName('use')->item(0)->hasAttribute('href'));
    }
}