<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 17/03/2017
 * Time: 15:23
 */

namespace WebSchema\Models\AMP\Rules;

abstract class SocialMedia extends Model
{
    const DEFAULT_HEIGHT = 0;
    const DEFAULT_WIDTH = 0;

    protected $platform;
    protected $regex;
    protected $element;
    protected $attribute;

    public function parse()
    {
        $this->addScript('amp-' . $this->platform);
        $this->replace();
    }

    protected function replace()
    {
        $elements = $this->document->getElementsByTagName($this->element);

        /**
         * @var \DOMElement[] $remove
         */
        $remove = [];

        foreach ($elements as $element) {
            $doc = new \DOMDocument();
            $node = $doc->importNode($element, true);
            $doc->appendChild($node);
            $html = $doc->saveHTML($node);

            if (preg_match('/' . $this->regex . '/s', $html, $matches)) {
                $this->addElement($element, $matches['uid']);
                $remove[] = $element;
            }
        }

        foreach ($remove as $element) {
            $element->parentNode->removeChild($element);
        }
    }

    /**
     * @param \DOMElement $refElement
     * @param  string     $uid
     * @return \DOMElement
     */
    protected function addElement(\DOMElement $refElement, $uid)
    {
        $element = $this->document->createElement('amp-' . $this->platform);
        $element->setAttribute($this->attribute, $uid);
        $this->setDefaultAttributes($element);

        foreach (['width', 'height', 'layout'] as $attribute) {
            if ($value = $refElement->getAttribute($attribute)) {
                $element->setAttribute($attribute, $value);
            }
        }

        $refElement->parentNode->insertBefore($element, $refElement);

        return $element;
    }

    /**
     * @param \DOMElement $element
     */
    protected function setDefaultAttributes(\DOMElement $element)
    {
        //set defaults
        if (!$element->getAttribute('width')) {
            $element->setAttribute('width', static::DEFAULT_WIDTH);
        }

        if (!$element->getAttribute('height')) {
            $element->setAttribute('height', static::DEFAULT_HEIGHT);
        }

        if (!$element->getAttribute('layout')) {
            $element->setAttribute('layout', 'responsive');
        }
    }
}