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
        $head = $this->document->createElement('head');
        $oldHead = $this->document->getElementsByTagName('head')->item(0);

        $this->addDefaultHeadElements($head);

        $this->addFonts($head);
        $this->addJSON($head, $oldHead);

        //add title
        $head->appendChild($oldHead->getElementsByTagName('title')->item(0));

        $oldHead->parentNode->replaceChild($head, $oldHead);
    }

    private function addDefaultHeadElements(\DOMElement $head)
    {
        $charset = $this->document->createElement('meta');
        $charset->setAttribute('charset', 'utf-8');
        $head->appendChild($charset);

        $script = $this->document->createElement('script');
        $script->setAttribute('src', WEB_SCHEMA_AMP_JS_FRAMEWORK . '.js');
        $script->setAttribute('async', '');
        $head->appendChild($script);

        $canonical = $this->document->createElement('link');
        $canonical->setAttribute('rel', 'canonical');
        $canonical->setAttribute('href', site_url(Route::getInstance()->getURI()));
        $head->appendChild($canonical);

        $viewport = $this->document->createElement('meta');
        $viewport->setAttribute('name', 'viewport');
        $viewport->setAttribute('content', WEB_SCHEMA_AMP_VIEWPORT);
        $head->appendChild($viewport);

        //add custom styles
        $style = (new HTML5())->loadHTMLFragment(WEB_SCHEMA_AMP_STYLE);
        $head->appendChild($this->document->importNode($style->firstChild, true));

        $style = (new HTML5())->loadHTMLFragment(WEB_SCHEMA_AMP_STYLE_NO_SCRIPT);
        $head->appendChild($this->document->importNode($style->firstChild, true));
    }

    private function addFonts(\DOMElement $head)
    {
        $xpath = new \DOMXPath($this->document);
        $xpath->registerNamespace('html', HTML5\Parser\DOMTreeBuilder::NAMESPACE_HTML);

        $fonts = $xpath->query("//html:link[@rel='stylesheet'][starts-with(@href, 'https://fonts.googleapis.com') or 
            starts-with(@href, 'https://cloud.typography.com') or starts-with(@href, 'https://fast.fonts.net') or 
            starts-with(@href, 'https://maxcdn.bootstrapcdn.com')]");

        foreach ($fonts as $font) {
            $head->appendChild($font);
        }
    }

    private function addJSON(\DOMElement $head, \DOMElement $oldHead)
    {
        $scripts = $oldHead->getElementsByTagName('script');

        foreach ($scripts as $script) {
            /**
             * @var \DOMElement $script
             */
            if ($script->getAttribute('type') == 'application/ld+json') {
                $head->appendChild($script);
            }
        }
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