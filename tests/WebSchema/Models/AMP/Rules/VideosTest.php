<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 06/03/2017
 * Time: 16:42
 */

namespace WebSchema\Tests\Models\AMP\Rules;

use Masterminds\HTML5;
use WebSchema\Models\AMP\Rules\Videos;

class VideosTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $html = <<<HERE
<html><head></head>
<body>
<video poster="https://archive.org/download/WebmVp8Vorbis/webmvp8.gif" controls="controls" width="480" height="150">
<source src="https://archive.org/download/WebmVp8Vorbis/webmvp8.webm" type="video/webm" />
<source src="https://archive.org/download/WebmVp8Vorbis/webmvp8_512kb.mp4" type="video/mp4" />
<source src="https://archive.org/download/WebmVp8Vorbis/webmvp8.ogv" type="video/ogg" />
Your browser doesn&#8217;t support HTML5 video tag.
</video>
</body>
</html>
HERE;

        $document = (new HTML5())->loadHTML($html);
        $rule = new Videos($document);

        $this->assertEquals(1, $document->getElementsByTagName('video')->length);

        $rule->parse();

        $this->assertEquals(0, $document->getElementsByTagName('video')->length);

        $video = $document->getElementsByTagName('amp-video')->item(0);
        $this->assertInstanceOf(\DOMElement::class, $video);
        $this->assertEquals(4, $video->childNodes->length);
        $this->assertTrue($document->getElementsByTagName('div')->item(0)->hasAttribute('fallback'));
    }
}