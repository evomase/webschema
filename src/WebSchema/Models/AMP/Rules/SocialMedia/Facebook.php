<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 24/04/2017
 * Time: 15:31
 */

namespace WebSchema\Models\AMP\Rules\SocialMedia;

use Masterminds\HTML5\Parser\DOMTreeBuilder;
use WebSchema\Models\AMP\Rules\SocialMedia;

class Facebook extends SocialMedia
{
    const DEFAULT_HEIGHT = 574;
    const DEFAULT_WIDTH = 552;

    protected $platform = 'facebook';
    protected $regex = '<div[^>]+data-href="(?P<uid>https:\/\/[^"]+.facebook.com[^"]+).+?<\/div>';
    protected $attribute = 'data-href';

    protected function getElements()
    {
        $xpath = new \DOMXPath($this->document);
        $xpath->registerNamespace('html', DOMTreeBuilder::NAMESPACE_HTML);
        $expression = "//html:div[starts-with(@class, 'fb-')][contains(@data-href, '.facebook.com')]";

        return $xpath->query($expression);
    }

    /**
     * @param \DOMElement $refElement
     * @param string      $uid
     * @return \DOMElement
     */
    protected function addElement(\DOMElement $refElement, $uid)
    {
        $element = parent::addElement($refElement, $uid);

        if (strpos($uid, 'videos')) {
            $element->setAttribute('data-embed-as', 'video');
        }

        return $element;
    }
}