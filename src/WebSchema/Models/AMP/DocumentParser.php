<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 26/02/2017
 * Time: 15:17
 */

namespace WebSchema\Models\AMP;

use Masterminds\HTML5;
use WebSchema\Models\AMP\Interfaces\Rule;
use WebSchema\Models\AMP\Rules\AdminBar;
use WebSchema\Models\AMP\Rules\Attributes;
use WebSchema\Models\AMP\Rules\Document;
use WebSchema\Models\AMP\Rules\Form;
use WebSchema\Models\AMP\Rules\Images;
use WebSchema\Models\AMP\Rules\SVG;

class DocumentParser
{
    /**
     * @var Rule[]
     */
    private static $rules = [
        Document::class, //Always first to be processed

        Images::class,
        AdminBar::class,
        Attributes::class,
        Form::class,
        SVG::class
    ];

    /**
     * @var \DOMDocument
     */
    private $document;
    private $html;

    /**
     * @var HTML5
     */
    private $parser;

    /**
     * DocumentParser constructor.
     * @param HTML5  $parser
     * @param string $html
     */
    public function __construct(HTML5 $parser, $html)
    {
        $this->html = $html;
        $this->parser = $parser;
        $this->document = $this->parser->loadHTML($this->html);
    }

    public function parse()
    {
        foreach (self::$rules as $class) {
            /**
             * @var Rule $rule
             */
            $rule = new $class($this->document);
            $rule->parse();
        }

        return $this->parser->saveHTML($this->document);
    }
}