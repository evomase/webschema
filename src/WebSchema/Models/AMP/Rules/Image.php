<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 26/02/2017
 * Time: 15:22
 */

namespace WebSchema\Models\AMP\Rules;

class Image extends Model
{
    const TAG_NAME = 'amp-img';

    public function parse()
    {
        $images = $this->document->getElementsByTagName('img');

        foreach ($images as $image) {
            $this->replace($image);
        }
    }

    private function replace(\DOMElement $image)
    {
        $element = $this->document->createElement(self::TAG_NAME);
        $child = $this->document->createElement('noscript');

        foreach (['src', 'height', 'width', 'alt'] as $attribute) {
            $element->setAttribute($attribute, $image->getAttribute($attribute));
            $child->setAttribute($attribute, $image->getAttribute($attribute));
        }

        $element->setAttribute('layout', 'responsive');
        $element->appendChild($child);

        $image->parentNode->replaceChild($element, $image);
    }
}