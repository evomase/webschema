<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 17/03/2017
 * Time: 15:33
 */

namespace WebSchema\Models\AMP\Rules\SocialMedia;

use WebSchema\Models\AMP\Rules\SocialMedia;

class Twitter extends SocialMedia
{
    const DEFAULT_HEIGHT = 392;
    const DEFAULT_WIDTH = 320;

    protected $platform = 'twitter';
    protected $regex = '<blockquote[^>]+twitter-tweet[^>]+?>.+https:\/\/twitter.com\/[^\s]+\/(?P<uid>[^"\/]+).+?<\/blockquote>';
    protected $element = 'blockquote';
    protected $attribute = 'data-tweetid';
}