<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 27/02/2017
 * Time: 14:16
 */

namespace WebSchema\Models\AMP\Rules;

use WebSchema\Models\WP\Settings;

class Forms extends Model
{
    const DEFAULT_TARGET = '_top';
    const TAG_NAME = 'amp-form';
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
            $this->addScript(self::TAG_NAME);
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

    private function handleTarget()
    {
        foreach ($this->forms as $form) {
            /**
             * @var \DOMElement $form
             */
            $target = $form->getAttribute('target');
            $target = (in_array($target, [self::DEFAULT_TARGET, '_blank'])) ? $target : self::DEFAULT_TARGET;

            $form->setAttribute('target', $target);
        }
    }
}