<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 26/02/2017
 * Time: 19:43
 */

namespace WebSchema\Models\AMP\Rules;

use WebSchema\Models\AMP\Route;

class Document extends Model
{
    public function parse()
    {
        $this->cleanHTMLTag();
        $this->cleanHead();
    }

    private function cleanHTMLTag()
    {
        $html = $this->document->getElementsByTagName('html')->item(0);

        foreach ($html->attributes as $name => $value) {
            $html->removeAttribute($name);
        }

        //for some reason, class isn't in the list of attributes
        $html->removeAttribute('class');
        $html->setAttribute('amp', '');
    }

    private function cleanHead()
    {
        $head = $this->document->getElementsByTagName('head')->item(0);

        $element = $this->document->createElement('head');
        $this->addDefaultHeadElements($element);

        $scripts = $head->getElementsByTagName('script');

        foreach ($scripts as $script) {
            /**
             * @var \DOMElement $script
             */
            if ($script->getAttribute('type') == 'application/ld+json') {
                $element->appendChild($script);
            }
        }

        $element->appendChild($head->getElementsByTagName('title')->item(0));
        $head->parentNode->replaceChild($element, $head);
    }

    private function addDefaultHeadElements(\DOMElement $element)
    {
        $charset = $this->document->createElement('meta');
        $charset->setAttribute('charset', 'utf-8');
        $element->appendChild($charset);

        $script = $this->document->createElement('script');
        $script->setAttribute('src', 'https://cdn.ampproject.org/v0.js');
        $script->setAttribute('async', '');
        $element->appendChild($script);

        $canonical = $this->document->createElement('link');
        $canonical->setAttribute('rel', 'canonical');
        $canonical->setAttribute('href', site_url(Route::getURI()));
        $element->appendChild($canonical);

        $viewport = $this->document->createElement('meta');
        $viewport->setAttribute('name', 'viewport');
        $viewport->setAttribute('content', WEB_SCHEMA_AMP_VIEWPORT);
        $element->appendChild($viewport);
    }
}