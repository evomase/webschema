<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 26/02/2017
 * Time: 15:25
 */

namespace WebSchema\Models\AMP\Rules;

use WebSchema\Models\AMP\Interfaces\Rule;

abstract class Model implements Rule
{
    /**
     * @var \DOMDocument
     */
    protected $document;

    public function __construct(\DOMDocument $document)
    {
        $this->document = $document;
    }

    /**
     * @param string $tag
     */
    protected function addScript($tag)
    {
        $head = $this->document->getElementsByTagName('head')->item(0);

        if ($head) {
            $script = $this->document->createElement('script');
            $script->setAttribute('custom-element', $tag);
            $script->setAttribute('async', '');
            $script->setAttribute('src', WEB_SCHEMA_AMP_JS_FRAMEWORK . '/' . $tag . '-0.1.js');

            $head->appendChild($script);
        }
    }
}