<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 17/03/2017
 * Time: 17:15
 */

namespace WebSchema\Tests\Models\AMP\Rules\SocialMedias;

use Masterminds\HTML5;
use WebSchema\Models\AMP\Rules\SocialMedia\Facebook;

class FacebookTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $html = <<<HERE
<html><head></head>
<body>
<div class="fb-video" data-href="https://www.facebook.com/facebook/videos/10153231379946729/" data-width="500" data-show-text="false">
<blockquote cite="https://www.facebook.com/facebook/videos/10153231379946729/" class="fb-xfbml-parse-ignore">
<a href="https://www.facebook.com/facebook/videos/10153231379946729/">How to Share With Just Friends</a><p>How to share with just friends.</p>Posted by 
<a href="https://www.facebook.com/facebook/">Facebook</a> on Friday, 5 December 2014</blockquote></div>
</body>
</html>
HERE;
        $document = (new HTML5())->loadHTML($html);
        $rule = new Facebook($document);
        $rule->parse();

        $this->assertEquals(0, $document->getElementsByTagName('div')->length);

        $head = $document->getElementsByTagName('head')->item(0);
        $this->assertEquals('amp-facebook',
            $head->getElementsByTagName('script')->item(0)->getAttribute('custom-element'));

        $this->assertEquals(1, $document->getElementsByTagName('amp-facebook')->length);
    }
}