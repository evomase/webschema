<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 01/03/2017
 * Time: 18:33
 */

namespace WebSchema\Models\AMP\Rules;

use Masterminds\HTML5\Parser\DOMTreeBuilder;

class Videos extends Model
{
    const TAG_NAME = 'amp-video';

    public function parse()
    {
        $videos = $this->document->getElementsByTagName('video');

        if ($videos->length) {
            $this->addScript(self::TAG_NAME);

            for ($i = 0; $i < $videos->length;) {
                $video = $videos->item($i);
                $this->replace($video);
            }
        }
    }

    private function replace(\DOMElement $video)
    {
        $xpath = new \DOMXPath($this->document);
        $xpath->registerNamespace('html', DOMTreeBuilder::NAMESPACE_HTML);

        $sources = $xpath->query('./html:source', $video);
        $fallback = ($fallback = $xpath->query('./text()', $video)) ? $fallback->item(0)->textContent : '';

        $element = $this->document->createElement(self::TAG_NAME);

        foreach ($video->attributes as $attribute) {
            /**
             * @var \DOMAttr $attribute
             */
            if ($attribute->value) {
                $element->setAttribute($attribute->name, $attribute->value);
            }

            foreach ($sources as $source) {
                $element->appendChild($source);
            }
        }

        if ($element->getAttribute('layout')) {
            $element->setAttribute('layout', 'responsive');
        }

        if ($fallback) {
            $div = $this->document->createElement('div');
            $div->setAttribute('fallback', '');

            $p = $this->document->createElement('p');
            $p->textContent = $fallback;

            $div->appendChild($p);

            $element->appendChild($div);
        }

        $video->parentNode->replaceChild($element, $video);
    }
}