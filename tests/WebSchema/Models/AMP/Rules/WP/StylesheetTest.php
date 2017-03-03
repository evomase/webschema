<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 03/03/2017
 * Time: 14:15
 */

namespace WebSchema\Tests\Models\AMP\Rules\WP;

use Masterminds\HTML5;
use WebSchema\Models\AMP\Rules\WP\Stylesheet;

class StylesheetTest extends \PHPUnit_Framework_TestCase
{
    public function testApplyImportantRule()
    {
        $css = <<<HERE
.screen-reader-text {
	clip: rect(1px, 1px, 1px, 1px);
	height: 1px;
	overflow: hidden;
	position: absolute !important;
	width: 1px;
	word-wrap: normal !important; /* Many screen reader and browser combinations announce broken words as they would appear visually. */
}

.screen-reader-text:focus {
	background-color: #f1f1f1;
	-webkit-border-radius: 3px;
	font-size: 14px !important; /* !important to overwrite inline styles */
	font-size: 0.875rem !important;
	margin: 4px 4px 0 0 !important;
	padding: 4px 10px 5px !important;
	border-radius: 3px;
	-webkit-box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.6);
	box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.6);
	clip: auto !important;
}
HERE;

        $expected = <<<HERE
.screen-reader-text {
	clip: rect(1px, 1px, 1px, 1px);
	height: 1px;
	overflow: hidden;
	width: 1px; /* Many screen reader and browser combinations announce broken words as they would appear visually. */
}

.screen-reader-text:focus {
	background-color: #f1f1f1;
	-webkit-border-radius: 3px; /* !important to overwrite inline styles */
	border-radius: 3px;
	-webkit-box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.6);
	box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.6);
}
HERE;

        $this->assertEquals($expected, Stylesheet::applyImportantRule($css));
    }

    public function testApplyMediaRule()
    {
        $css = <<<HERE
@media screen and ( max-width: 48.875em ) and ( min-width: 48em ) {

	.admin-bar .site-navigation-fixed.navigation-top,
	.admin-bar .site-navigation-hidden.navigation-top {
		top: 46px;
	}
}

/*--------------------------------------------------------------
20.0 Print
--------------------------------------------------------------*/

@media print {

	/* Hide elements */

	form,
	button,
	input,
	select,
	textarea,
	.navigation-top,
	.social-navigation,
	#secondary,
	.content-bottom-widgets,
	.header-image,
	.panel-image-prop,
	.icon-thumb-tack,
	.page-links,
	.edit-link,
	.post-navigation,
	.pagination.navigation,
	.comments-pagination,
	.comment-respond,
	.comment-edit-link,
	.comment-reply-link,
	.comment-metadata .edit-link,
	.pingback .edit-link,
	.site-footer aside.widget-area,
	.site-info {
		display: none !important;
	}
}
HERE;

        $expected = <<<HERE
@media screen and ( max-width: 48.875em ) and ( min-width: 48em ) {

	.admin-bar .site-navigation-fixed.navigation-top,
	.admin-bar .site-navigation-hidden.navigation-top {
		top: 46px;
	}
}

/*--------------------------------------------------------------
20.0 Print
--------------------------------------------------------------*/
HERE;

        $this->assertEquals($expected, Stylesheet::applyMediaRule($css));
    }

    public function testApplyPropertyRule()
    {
        $css = <<<HERE
.custom-header-media:before {
	background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 75%, rgba(0, 0, 0, 0.3) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#00000000", endColorstr="#4d000000", GradientType=0); /* IE6-9 */
	right: 0;
	z-index: 2;
}

.gallery-item a:hover img,
.gallery-item a:focus img {
	-webkit-filter: opacity(60%);
	filter: opacity(60%);
}

.exampleone {
  -moz-binding: url(http://www.example.org/xbl/htmlBindings.xml#radiobutton);
}
HERE;

        $expected = <<<HERE
.custom-header-media:before {
	background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 75%, rgba(0, 0, 0, 0.3) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */ /* IE6-9 */
	right: 0;
	z-index: 2;
}

.gallery-item a:hover img,
.gallery-item a:focus img {
}

.exampleone {
}
HERE;

        $this->assertEquals($expected, Stylesheet::applyPropertyRule($css));
    }

    public function testApplyTransitionRule()
    {
        $css = <<<HERE
.box {
    border-style: solid;
    border-width: 1px;
    display: block;
    width: 100px;
    height: 100px;
    transition: transform 4s;
    transition: margin-left 4s 1s;
    transition: margin-left 4s ease-in-out 1s;
    transition: margin-left 4s, opacity 1s;
    transition: all 0.5s ease-out;
    background-color: #0000FF;
    -webkit-transition: width 2s, height 2s, background-color 2s, -webkit-transform 2s;
    transition: width 2s, height 2s, -moz-transform, transform 2s;
}
HERE;

        $expected = <<<HERE
.box {
    border-style: solid;
    border-width: 1px;
    display: block;
    width: 100px;
    height: 100px;
    transition: transform 4s;
    transition: opacity 1s;
    background-color: #0000FF;
    -webkit-transition: -webkit-transform 2s;
    transition: -moz-transform, transform 2s;
}
HERE;

        $this->assertEquals($expected, Stylesheet::applyTransitionRule($css));
    }

    public function testApplySelectorRule()
    {
        $css = <<<HERE
* [lang^=en] {
  color:green;
}

*.warning {
  color:red;
}

*#maincontent {
  border: 1px solid blue;
}

.floating {
  float: left
}

*#maincontent {
  border: 5/2;
}

*#maincontent {
  border: 5*2;
}

.wp-caption img[class*="wp-image-"] {
	display: block;
	margin-left: auto;
	margin-right: auto;
}

/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#000000+0,000000+100&0+0,0.3+100 */ /* FF3.6-15 */

/* Hide the menu on small screens when JavaScript is available.
 * It only works with JavaScript.
 */

.js .main-navigation ul,
.main-navigation .menu-item-has-children > a > .icon,
.main-navigation .page_item_has_children > a > .icon,
.main-navigation ul a > .icon {
	display: none;
}

body *:not(p) { color: green; }

.floating,
.what * {
  float: right
}

.what *,
*.warning,
.what *,
what {
  float: left
}

/* automatically clear the next sibling after a floating element */
.floating + * {
  clear: left;
}

.floating + * {
  clear: left;
}
HERE;

        $expected = <<<HERE


.floating {
  float: left
}

.wp-caption img[class*="wp-image-"] {
	display: block;
	margin-left: auto;
	margin-right: auto;
}

/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#000000+0,000000+100&0+0,0.3+100 */ /* FF3.6-15 */

/* Hide the menu on small screens when JavaScript is available.
 * It only works with JavaScript.
 */

.js .main-navigation ul,
.main-navigation .menu-item-has-children > a > .icon,
.main-navigation .page_item_has_children > a > .icon,
.main-navigation ul a > .icon {
	display: none;
}

.floating{
  float: right
}



what {
  float: left
}

/* automatically clear the next sibling after a floating element */
HERE;

        $this->assertEquals($expected, Stylesheet::applySelectorRule($css));
    }

    public function testCleanCSS()
    {
        $css = <<<HERE
[lang^=en] {
    color:green;
}

.warning {
    color:red;
}

#maincontent {
    border: 1px solid blue; /* automatically clear the next sibling after a floating element */
}

.floating {
    float: left
}

/* automatically clear the next sibling after a floating element */

#maincontent {
    border: 5/2;
}

#maincontent {
}

.wp-caption img[class*="wp-image-"] {
    display: block;
    margin-left: auto;
    margin-right: auto;
}
HERE;

        $expected = '[lang^=en] {color:green;}.warning {color:red;}#maincontent {border: 1px solid blue;}'
            . '.floating {float: left}#maincontent {border: 5/2;}.wp-caption img[class*="wp-image-"] {display: block;'
            . 'margin-left: auto;margin-right: auto;}';

        $this->assertEquals($expected, Stylesheet::cleanCSS($css));
    }

    public function testParse()
    {
        $html = '<html><head></head><body></body></html>';
        $document = (new HTML5())->loadHTML($html);

        $rule = new Stylesheet($document);
        $rule->parse();
        $style = $document->getElementsByTagName('style')->item(0);

        $this->assertInstanceOf(\DOMElement::class, $style);
        $this->assertNotEmpty($style->textContent);
    }
}