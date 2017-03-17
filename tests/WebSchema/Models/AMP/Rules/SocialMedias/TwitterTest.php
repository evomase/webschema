<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 17/03/2017
 * Time: 16:18
 */

namespace WebSchema\Tests\Models\AMP\Rules\SocialMedias;

use Masterminds\HTML5;
use WebSchema\Models\AMP\Rules\SocialMedias\Twitter;

class TwitterTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $html = <<<HERE
<html><head></head>
<body>
<blockquote class="twitter-tweet" data-lang="en"><p lang="en" dir="ltr">Imagine waking up and having no recollection of the day before... - <a href="https://t.co/IrXpuMT2dq">https://t.co/IrXpuMT2dq</a> <a href="https://twitter.com/hashtag/missing?src=hash">#missing</a> <a href="https://twitter.com/hashtag/photography?src=hash">#photography</a> <a href="https://t.co/52BNUj7z18">pic.twitter.com/52BNUj7z18</a></p>&mdash; David Ogilo (@evomase) <a href="https://twitter.com/evomase/status/817388955305996293">January 6, 2017</a></blockquote>
<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
</body>
</html>
HERE;
        $document = (new HTML5())->loadHTML($html);
        $rule = new Twitter($document);
        $rule->parse();

        $this->assertEquals(0, $document->getElementsByTagName('blockquote')->length);

        $head = $document->getElementsByTagName('head')->item(0);
        $this->assertEquals('amp-twitter',
            $head->getElementsByTagName('script')->item(0)->getAttribute('custom-element'));

        $this->assertEquals(1, $document->getElementsByTagName('amp-twitter')->length);
    }
}