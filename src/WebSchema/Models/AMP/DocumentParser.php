<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 26/02/2017
 * Time: 15:17
 */

namespace WebSchema\Models\AMP;

use WebSchema\Models\AMP\Interfaces\Rule;
use WebSchema\Models\AMP\Rules\Document;
use WebSchema\Models\AMP\Rules\Image;

class DocumentParser
{
    /**
     * @var Rule[]
     */
    private static $rules = [
        Document::class,
        Image::class
    ];

    /**
     * @var \DOMDocument
     */
    private $document;

    public function __construct(\DOMDocument $document)
    {
        $this->document = $document;
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

        return preg_replace('/=""/', '', $this->document->saveHTML());
    }
}