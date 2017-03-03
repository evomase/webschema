<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 03/03/2017
 * Time: 17:16
 */

namespace WebSchema\Tests\Models\AMP\Rules;

use Masterminds\HTML5;
use WebSchema\Models\AMP\Route;
use WebSchema\Models\AMP\Rules\Document;

class DocumentTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        Route::boot();
    }

    public function testCleanHTMLTag()
    {
        $html = '<html lang="en-US" class="no-js no-svg"><body><p>Hello world</p></body></html>';
        $document = (new HTML5())->loadHTML($html);

        $parser = new Document($document);
        $parser->parse();

        $this->assertTrue($document->getElementsByTagName('html')->item(0)->hasAttribute('amp'));
    }

    public function testCleanHead()
    {
        $html = <<<HERE
<html lang="en-US" class="no-js no-svg">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>
<title>Hello world! &#8211; PHPUNIT</title>
<link rel='dns-prefetch' href='//fonts.googleapis.com' />
<link rel='dns-prefetch' href='//s.w.org' />
<link href='https://fonts.gstatic.com' crossorigin rel='preconnect' />
<script type="text/javascript">
			window._wpemojiSettings = {"baseUrl":"https:\/\/s.w.org\/images\/core\/emoji\/2.2.1\/72x72\/","ext":".png","svgUrl":"https:\/\/s.w.org\/images\/core\/emoji\/2.2.1\/svg\/","svgExt":".svg","source":{"concatemoji":"http:\/\/webschema.localhost\/wp-includes\/js\/wp-emoji-release.min.js?ver=4.7"}};
			!function(a,b,c){function d(a){var b,c,d,e,f=String.fromCharCode;if(!k||!k.fillText)return!1;switch(k.clearRect(0,0,j.width,j.height),k.textBaseline="top",k.font="600 32px Arial",a){case"flag":return k.fillText(f(55356,56826,55356,56819),0,0),!(j.toDataURL().length<3e3)&&(k.clearRect(0,0,j.width,j.height),k.fillText(f(55356,57331,65039,8205,55356,57096),0,0),b=j.toDataURL(),k.clearRect(0,0,j.width,j.height),k.fillText(f(55356,57331,55356,57096),0,0),c=j.toDataURL(),b!==c);case"emoji4":return k.fillText(f(55357,56425,55356,57341,8205,55357,56507),0,0),d=j.toDataURL(),k.clearRect(0,0,j.width,j.height),k.fillText(f(55357,56425,55356,57341,55357,56507),0,0),e=j.toDataURL(),d!==e}return!1}function e(a){var c=b.createElement("script");c.src=a,c.defer=c.type="text/javascript",b.getElementsByTagName("head")[0].appendChild(c)}var f,g,h,i,j=b.createElement("canvas"),k=j.getContext&&j.getContext("2d");for(i=Array("flag","emoji4"),c.supports={everything:!0,everythingExceptFlag:!0},h=0;h<i.length;h++)c.supports[i[h]]=d(i[h]),c.supports.everything=c.supports.everything&&c.supports[i[h]],"flag"!==i[h]&&(c.supports.everythingExceptFlag=c.supports.everythingExceptFlag&&c.supports[i[h]]);c.supports.everythingExceptFlag=c.supports.everythingExceptFlag&&!c.supports.flag,c.DOMReady=!1,c.readyCallback=function(){c.DOMReady=!0},c.supports.everything||(g=function(){c.readyCallback()},b.addEventListener?(b.addEventListener("DOMContentLoaded",g,!1),a.addEventListener("load",g,!1)):(a.attachEvent("onload",g),b.attachEvent("onreadystatechange",function(){"complete"===b.readyState&&c.readyCallback()})),f=c.source||{},f.concatemoji?e(f.concatemoji):f.wpemoji&&f.twemoji&&(e(f.twemoji),e(f.wpemoji)))}(window,document,window._wpemojiSettings);
		</script>
		<style type="text/css">
img.wp-smiley,
img.emoji {
	display: inline !important;
	border: none !important;
	box-shadow: none !important;
	height: 1em !important;
	width: 1em !important;
	margin: 0 .07em !important;
	vertical-align: -0.1em !important;
	background: none !important;
	padding: 0 !important;
}
</style>
<link rel='stylesheet' id='twentyseventeen-fonts-css'  href='https://fonts.googleapis.com/css?family=Libre+Franklin%3A300%2C300i%2C400%2C400i%2C600%2C600i%2C800%2C800i&#038;subset=latin%2Clatin-ext' type='text/css' media='all' />
<script type="application/ld+json">{"@context":"http://schema.org/","@type":"Article","author":{"@type":"Person","name":"admin","url":"http://www.davidogilo.co.uk"},"dateModified":"2017-02-23T16:53:47+00:00","datePublished":"2016-12-14T17:08:47+00:00","description":"Welcome to WordPress. This is your first post. Edit or delete it, then start writing!","headline":"Hello world!","image":{"@type":"ImageObject","url":"http://webschema.localhost/wp-content/uploads/2016/12/ModelTest.jpg","width":700,"height":2},"mainEntityOfPage":"http://webschema.localhost/index.php/2016/12/14/hello-world-2/","publisher":{"@type":"Organization","name":"PHPUNIT","logo":{"@type":"ImageObject","url":"http://webschema.localhost/wp-content/uploads/2017/02/PublisherImage.jpg","width":600,"height":60}}}</script>
</head>
<body>
<p>Hello world</p>
<!-- #page -->
<script type='text/javascript' src='http://webschema.localhost/wp-includes/js/admin-bar.min.js?ver=4.7'></script>
<script type='text/javascript'>
/* <![CDATA[ */
var twentyseventeenScreenReaderText = {"quote":"<svg class=\"icon icon-quote-right\" aria-hidden=\"true\" role=\"img\"> <use href=\"#icon-quote-right\" xlink:href=\"#icon-quote-right\"><\/use> <\/svg>"};
/* ]]> */
</script>
</body>
</html>
HERE;
        $document = (new HTML5())->loadHTML($html);

        $parser = new Document($document);
        $parser->parse();

        $xpath = new \DOMXPath($document);

        /**
         * @var \DOMElement $element
         */
        $this->assertInstanceOf(\DOMElement::class, $xpath->query('//meta[@charset="utf-8"]')->item(0));
        $this->assertInstanceOf(\DOMElement::class, $xpath->query('//script[@async]')->item(0));
        $this->assertInstanceOf(\DOMElement::class, $xpath->query('//link[@rel="canonical"]')->item(0));
        $this->assertInstanceOf(\DOMElement::class, $xpath->query('//meta[@name="viewport"]')->item(0));

//        $this->assertInstanceOf(\DOMElement::class, $xpath->query('//style[@amp-boilerplate]')->item(0));
//        $this->assertInstanceOf(\DOMElement::class, $xpath->query('//noscript/style[@amp-boilerplate]')->item(0));
//
//        //fonts
//        $this->assertInstanceOf(\DOMElement::class, $xpath->query('//link[@rel="stylesheet"]')->item(0));

        //json-ld
//        $this->assertInstanceOf(\DOMElement::class,
//            $xpath->query('//script[@type="application/ld+json"]')->item(0));

        $this->assertEquals(0, $xpath->query('//script[@type="text/javascript"]')->length);
        $this->assertEquals(0, $xpath->query('//comment()')->length);
        $this->assertEquals(0, $xpath->query('.//script', $document->getElementsByTagName('body')->item(0))->length);
    }
}