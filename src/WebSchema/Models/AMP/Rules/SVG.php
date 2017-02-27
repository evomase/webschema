<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 27/02/2017
 * Time: 14:41
 */

namespace WebSchema\Models\AMP\Rules;

class SVG extends Model
{
    public function parse()
    {
        $attributes = (new \DOMXPath($this->document))->query('//svg');

        echo '<pre>';
        print_r($attributes);
        print_r($this->document->getElementsByTagName('svg'));
        exit;
    }
}