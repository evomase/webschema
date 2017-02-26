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
}