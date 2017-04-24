<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 24/04/2017
 * Time: 18:49
 */

namespace WebSchema\Models\AMP\Rules\SocialMedia;

use WebSchema\Models\AMP\Rules\SocialMedia;

class YouTube extends SocialMedia
{
    const DEFAULT_HEIGHT = 270;
    const DEFAULT_WIDTH = 480;

    protected $platform = 'youtube';
    protected $regex = '<iframe[^>]+src="https:\/\/www.youtube.com\/embed\/(?P<uid>[^"]+).+?<\/iframe>';
    protected $element = 'iframe';
    protected $attribute = 'data-videoid';
}