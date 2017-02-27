<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 26/02/2017
 * Time: 19:43
 */

namespace WebSchema\Models\AMP\Rules;

use Masterminds\HTML5;
use WebSchema\Models\AMP\Route;

class Document extends Model
{
    public function parse()
    {
        $this->cleanHTMLTag();
        $this->cleanHead();
        $this->cleanBody();
    }

    private function cleanHTMLTag()
    {
        $html = $this->document->getElementsByTagName('html')->item(0);

        for ($i = 0; $html->attributes->length;) {
            $attribute = $html->attributes->item($i);

            /**
             * @var \DOMAttr $attribute
             */
            $html->removeAttributeNode($attribute);
        }

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
        $script->setAttribute('src', WEB_SCHEMA_AMP_FRAMEWORK . '.js');
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

        //add custom styles
        $style = (new HTML5())->loadHTMLFragment(WEB_SCHEMA_AMP_STYLE);
        $element->appendChild($this->document->importNode($style->firstChild, true));

        $style = (new HTML5())->loadHTMLFragment(WEB_SCHEMA_AMP_STYLE_NO_SCRIPT);
        $element->appendChild($this->document->importNode($style->firstChild, true));
    }

    private function cleanBody()
    {
        $body = $this->document->getElementsByTagName('body')->item(0);

        //remove all scripts
        $scripts = $body->getElementsByTagName('script');

        for ($i = 0; $i < $scripts->length;) {
            $script = $scripts->item($i);
            $script->parentNode->removeChild($script);
        }

        //remove all comments
        $comments = (new \DOMXPath($this->document))->query('//comment()', $body);

        if ($comments->length) {
            foreach ($comments as $comment) {
                $comment->parentNode->removeChild($comment);
            }
        }
    }
}