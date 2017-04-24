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

    /**
     * @var \DOMNodeList
     */
    protected $elements;

    public function parse()
    {
        $this->elements = $this->getElements();

        if ($this->elements->length) {
            $this->addScript('amp-' . $this->platform);
            $this->replace();
        }
    }

    /**
     * @return \DOMNodeList
     */
    protected function getElements()
    {
        return $this->document->getElementsByTagName($this->element);
    }

    protected function replace()
    {
        /**
         * @var \DOMElement[] $remove
         */
        $remove = [];

        foreach ($this->elements as $element) {
            $html = $this->document->saveHTML($element);

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

        foreach (['width', 'height', 'layout'] as $attribute) {
            if ($value = $refElement->getAttribute($attribute)) {
                $element->setAttribute($attribute, $value);
            } else {
                $this->setDefaultAttribute($attribute, $element);
            }
        }
        $refElement->parentNode->insertBefore($element, $refElement);

        return $element;
    }

    /**
     * @param string      $attribute
     * @param \DOMElement $element
     */
    protected function setDefaultAttribute($attribute, \DOMElement $element)
    {
        switch ($attribute) {
            case 'height':
                $element->setAttribute('height', static::DEFAULT_HEIGHT);
                break;
            case 'width':
                $element->setAttribute('width', static::DEFAULT_WIDTH);
                break;
            case 'layout':
                $element->setAttribute('layout', 'responsive');
                break;
        }
    }
}