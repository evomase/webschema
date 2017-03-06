<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 06/03/2017
 * Time: 16:05
 */

namespace WebSchema\Tests\Models\AMP\Rules;

use Masterminds\HTML5;
use WebSchema\Models\AMP\Rules\Forms;
use WebSchema\Models\WP\Settings;
use WebSchema\Services\WP\SettingsService;

class FormsTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        update_option(Settings::NAME, [
            Settings::FIELD_AMP => [
                Settings::FIELD_AMP_USE_SSL => true
            ]
        ]);

        SettingsService::boot();
    }

    public static function tearDownAfterClass()
    {
        Settings::reset();
    }

    public function testParse()
    {
        $html = <<<HERE
<html><head></head><body>
<form id="form1" action="http://example.com" method="post" target="_top"><input type="tel" name="FormTest"/></form>
<form id="form2" action="http://example.com" method="get" target="_random"><input type="tel" name="FormTest2"/></form>
</body></html>
HERE;
        $document = (new HTML5())->loadHTML($html);
        $rule = new Forms($document);
        $rule->parse();

        $form1 = $document->getElementById('form1');
        $this->assertRegExp('/^https:\/\//', $form1->getAttribute('action-xhr'));
        $this->assertEquals('_top', $form1->getAttribute('target'));

        $form2 = $document->getElementById('form2');
        $this->assertRegExp('/^https:\/\//', $form2->getAttribute('action'));
        $this->assertEquals(Forms::DEFAULT_TARGET, $form2->getAttribute('target'));

        $script = $document->getElementsByTagName('script')->item(0);
        $this->assertInstanceOf(\DOMElement::class, $script);
        $this->assertRegExp('/' . preg_quote(Forms::TAG_NAME . '-0.1.js', '/') . '$/', $script->getAttribute('src'));

        //disable SSL
        update_option(Settings::NAME, [
            Settings::FIELD_AMP => [
                Settings::FIELD_AMP_USE_SSL => false
            ]
        ]);

        $rule->parse();

        $this->assertEquals(0, $document->getElementsByTagName('form')->length);
    }
}