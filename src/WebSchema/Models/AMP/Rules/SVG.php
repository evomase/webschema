<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 27/02/2017
 * Time: 14:41
 */

namespace WebSchema\Models\AMP\Rules;

use Masterminds\HTML5\Parser\DOMTreeBuilder;

class SVG extends Model
{
    public function parse()
    {
        $xpath = new \DOMXPath($this->document);
        $xpath->registerNamespace('svg', DOMTreeBuilder::NAMESPACE_SVG);

        foreach ($attributes = $xpath->query('//svg:svg//@href') as $attribute) {
            /**
             * @var \DOMAttr $attribute
             */
            $attribute->ownerElement->removeAttributeNode($attribute);
        }
    }
}