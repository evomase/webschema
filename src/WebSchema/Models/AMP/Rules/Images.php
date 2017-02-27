<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 26/02/2017
 * Time: 15:22
 */

namespace WebSchema\Models\AMP\Rules;

class Images extends Model
{
    const TAG_NAME = 'amp-img';

    public function parse()
    {
        $images = $this->document->getElementsByTagName('img');

        //The DOMNodeList is updated when <img> is removed, so we always get the first item
        for ($i = 0; $i < $images->length;) {
            $image = $images->item($i);
            $this->replace($image);
        }
    }

    private function replace(\DOMElement $image)
    {
        $element = $this->document->createElement(self::TAG_NAME);

        foreach ($image->attributes as $attribute) {
            /**
             * @var \DOMAttr $attribute
             */
            if ($attribute->value) {
                $element->setAttribute($attribute->name, $attribute->value);
            }
        }

        $element->setAttribute('layout', 'responsive');

        $image->parentNode->replaceChild($element, $image);
    }
}