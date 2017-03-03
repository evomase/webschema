<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 03/03/2017
 * Time: 17:06
 */

namespace WebSchema\Tests\Models\AMP\Rules;

use Masterminds\HTML5;
use WebSchema\Models\AMP\Rules\Attributes;

class AttributesTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $html = '<div style="width: 100px;" on="amp-specific"><span onclick="console.log(\'hello world\');"></span></div>';
        $document = (new HTML5())->loadHTML($html);

        $parser = new Attributes($document);
        $parser->parse();

        $xpath = new \DOMXPath($document);

        $this->assertEquals(0, $xpath->query('//@style')->length);
        $this->assertEquals(0, $xpath->query('//@onclick')->length);
        $this->assertEquals(1, $xpath->query('//@on')->length);
    }
}