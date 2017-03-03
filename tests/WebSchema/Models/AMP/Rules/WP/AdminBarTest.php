<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 03/03/2017
 * Time: 14:05
 */

namespace WebSchema\Tests\Models\AMP\Rules\WP;

use Masterminds\HTML5;
use WebSchema\Models\AMP\Rules\WP\AdminBar;

class AdminBarTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $html = '<div id="wpadminbar" class="nojq nojs">
							<a class="screen-reader-shortcut" href="#wp-toolbar" tabindex="1">Skip to toolbar</a>
						<div class="quicklinks" id="wp-toolbar" role="navigation" aria-label="Toolbar" tabindex="0">
				<ul id="wp-admin-bar-root-default" class="ab-top-menu"></ul></div></div>';

        $document = (new HTML5())->loadHTML($html);
        $this->assertInstanceOf(\DOMElement::class, $document->getElementById('wpadminbar'));

        $rule = new AdminBar($document);
        $rule->parse();
        $this->assertEmpty($document->getElementById('wpadminbar'));
    }
}