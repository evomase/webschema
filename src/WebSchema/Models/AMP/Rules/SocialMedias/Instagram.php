<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 17/03/2017
 * Time: 16:50
 */

namespace WebSchema\Models\AMP\Rules\SocialMedias;

use WebSchema\Models\AMP\Rules\SocialMedia;

class Instagram extends SocialMedia
{
    protected $platform = 'instagram';
    protected $regex = '<blockquote[^>]+instagram-media[^>]+?>.+https:\/\/www\.instagram\.com\/p\/(?P<uid>[^"\/]+).+?<\/blockquote>';
    protected $element = 'blockquote';
    protected $attribute = 'data-shortcode';
}