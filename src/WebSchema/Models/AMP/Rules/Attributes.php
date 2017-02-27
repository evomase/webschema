<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 27/02/2017
 * Time: 13:26
 */

namespace WebSchema\Models\AMP\Rules;

class Attributes extends Model
{
    public function parse()
    {
        $this->removeInvalidAttributes();
    }

    private function removeInvalidAttributes()
    {
        $attributes = (new \DOMXPath($this->document))->query("//@style|//@*[starts-with(name(), 'on')][string-length(name()) > 2]");

        foreach ($attributes as $attribute) {
            /**
             * @var \DOMAttr $attribute
             */
            $attribute->ownerElement->removeAttributeNode($attribute);
        }
    }
}