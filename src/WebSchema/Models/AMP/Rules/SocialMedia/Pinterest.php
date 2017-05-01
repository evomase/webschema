<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 01/05/2017
 * Time: 17:58
 */

namespace WebSchema\Models\AMP\Rules\SocialMedia;

use Masterminds\HTML5\Parser\DOMTreeBuilder;
use WebSchema\Models\AMP\Rules\SocialMedia;

class Pinterest extends SocialMedia
{
    const DEFAULT_HEIGHT = 330;
    const DEFAULT_WIDTH = 245;

    protected $platform = 'pinterest';
    protected $regex = '<a[^>]+href="(?P<uid>https:\/\/[^"]+.pinterest.com\/pin\/[^"]+).+?<\/a>';
    protected $attribute = 'data-url';
    protected $dataAttributePrefix = 'pin';

    /**
     * @return \DOMNodeList
     */
    protected function getElements()
    {
        $xpath = new \DOMXPath($this->document);
        $xpath->registerNamespace('html', DOMTreeBuilder::NAMESPACE_HTML);
        $expression = "//html:a[contains(@href, '.pinterest.com/pin')]";

        return $xpath->query($expression);
    }
}