<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 27/02/2017
 * Time: 14:16
 */

namespace WebSchema\Models\AMP\Rules;

use WebSchema\Models\WP\Settings;

class Form extends Model
{
    /**
     * @var \DOMNodeList
     */
    private $forms;
    private $useSSL = false;

    public function parse()
    {
        $this->init();

        if ($this->useSSL) {
            $this->handleAction();
            $this->addScript();
            $this->handleTarget();
        }
    }

    private function init()
    {
        $this->useSSL = Settings::get(Settings::FIELD_AMP)[Settings::FIELD_AMP_USE_SSL];
        $this->forms = $this->document->getElementsByTagName('form');

        //Disable amp-form functionality if SSL is not active on web server
        if (!$this->useSSL) {
            for ($i = 0; $i < $this->forms->length;) {
                $form = $this->forms->item($i);
                $form->parentNode->removeChild($form);
            }
        }
    }

    private function handleAction()
    {
        foreach ($this->forms as $form) {
            /**
             * @var \DOMElement $form
             */
            $action = str_replace('http:', 'https:', $form->getAttribute('action'));

            if (strtolower($form->getAttribute('method')) == 'post') {
                $form->setAttribute('action-xhr', $action);
                $form->removeAttribute('action');
            } else {
                $form->setAttribute('action', $action);
            }

            $form->setAttribute('target', '_top');
        }
    }

    private function addScript()
    {
        $head = $this->document->getElementsByTagName('head')->item(0);

        $script = $this->document->createElement('script');
        $script->setAttribute('custom-element', 'amp-form');
        $script->setAttribute('async', '');
        $script->setAttribute('src', WEB_SCHEMA_AMP_FRAMEWORK . '/amp-form-0.1.js');

        $head->appendChild($script);
    }

    private function handleTarget()
    {
        foreach ($this->forms as $form) {
            /**
             * @var \DOMElement $form
             */
            $target = $form->getAttribute('target');
            $target = (in_array($target, ['_top', '_blank'])) ? $target : '_top';

            $form->setAttribute('target', $target);
        }
    }
}