<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 27/02/2017
 * Time: 14:16
 */

namespace WebSchema\Models\AMP\Rules;

class Form extends Model
{
    public function parse()
    {
        $forms = $this->document->getElementsByTagName('form');

        if ($forms->length) {
            $head = $this->document->getElementsByTagName('head')->item(0);

            $script = $this->document->createElement('script');
            $script->setAttribute('custom-element', 'amp-form');
            $script->setAttribute('async', '');
            $script->setAttribute('src', WEB_SCHEMA_AMP_FRAMEWORK . '/amp-form-0.1.js');

            $head->appendChild($script);
        }
    }
}