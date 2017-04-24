<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 17/03/2017
 * Time: 16:50
 */

namespace WebSchema\Models\AMP\Rules\SocialMedia;

use WebSchema\Models\AMP\Rules\SocialMedia;

class Instagram extends SocialMedia
{
    const DEFAULT_HEIGHT = 392;
    const DEFAULT_WIDTH = 320;

    protected $platform = 'instagram';
    protected $regex = '<blockquote[^>]+instagram-media[^>]+?>.+https:\/\/www\.instagram\.com\/p\/(?P<uid>[^"\/]+).+?<\/blockquote>';
    protected $element = 'blockquote';
    protected $attribute = 'data-shortcode';

    /**
     * @param \DOMElement $refElement
     * @param string      $uid
     * @return \DOMElement
     */
    protected function addElement(\DOMElement $refElement, $uid)
    {
        $element = parent::addElement($refElement, $uid);
        $element->setAttribute('data-captioned', 'true');

        return $element;
    }
}