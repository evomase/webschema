<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 17/03/2017
 * Time: 17:15
 */

namespace WebSchema\Tests\Models\AMP\Rules\SocialMedias;

use Masterminds\HTML5;
use WebSchema\Models\AMP\Rules\SocialMedia\YouTube;

class YouTubeTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $html = <<<HERE
<html><head></head>
<body>
<iframe width="560" height="315" src="https://www.youtube.com/embed/xSSL_0DKkRI" frameborder="0" allowfullscreen></iframe>
</body>
</html>
HERE;
        $document = (new HTML5())->loadHTML($html);
        $rule = new YouTube($document);
        $rule->parse();

        $this->assertEquals(0, $document->getElementsByTagName('iframe')->length);

        $head = $document->getElementsByTagName('head')->item(0);
        $this->assertEquals('amp-youtube',
            $head->getElementsByTagName('script')->item(0)->getAttribute('custom-element'));

        $this->assertEquals(1, $document->getElementsByTagName('amp-youtube')->length);
    }
}